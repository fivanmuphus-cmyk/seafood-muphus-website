<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name', 'city', 'address', 'latitude', 'longitude', 'maps_url',
        'whatsapp', 'opens_at', 'closes_at', 'hours_note', 'photo_path',
        'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function whatsappUrl(): ?string
    {
        return $this->whatsapp ? 'https://wa.me/' . $this->whatsapp : null;
    }

    public function directionsUrl(): ?string
    {
        if ($coords = $this->coordinates()) {
            return 'https://www.google.com/maps/dir/?api=1&destination=' . $coords[0] . ',' . $coords[1];
        }

        return $this->maps_url ?: null;
    }

    /** Nama tempat persis seperti di Google Maps, diambil dari tautan ".../maps/place/NAMA/..." (bila ada). */
    public function googlePlaceName(): ?string
    {
        if (preg_match('~/maps/place/([^/@?]+)~', (string) $this->maps_url, $m)) {
            $name = trim(str_replace('+', ' ', rawurldecode($m[1])));

            return $name !== '' ? $name : null;
        }

        return null;
    }

    /** ID tempat (CID, angka desimal) dari tautan Google Maps panjang; null bila tidak ada. */
    public function googleCid(): ?string
    {
        if (preg_match('~!1s0x[0-9a-f]+:(0x[0-9a-f]+)!8m2!3d-?\d+\.\d+!4d-?\d+\.\d+~i', (string) $this->maps_url, $m)) {
            return $this->hexToDecimal($m[1]);
        }

        return null;
    }

    /** Ubah heksadesimal (mis. 0x99a2f5991c1452db) ke angka desimal tanpa butuh ekstensi bcmath/gmp. */
    private function hexToDecimal(string $hex): string
    {
        $digits = [0]; // digit desimal, urutan terbalik

        foreach (str_split(strtolower(substr($hex, 2))) as $char) {
            $carry = hexdec($char);

            foreach ($digits as $i => $d) {
                $value = $d * 16 + $carry;
                $digits[$i] = $value % 10;
                $carry = intdiv($value, 10);
            }

            while ($carry > 0) {
                $digits[] = $carry % 10;
                $carry = intdiv($carry, 10);
            }
        }

        return implode('', array_reverse($digits));
    }

    /** Koordinat [lat, lng]: dari isian Latitude/Longitude, atau (cadangan) dibaca dari tautan Google Maps. */
    public function coordinates(): ?array
    {
        if ($this->latitude !== null && $this->longitude !== null) {
            return [(float) $this->latitude, (float) $this->longitude];
        }

        $url = (string) $this->maps_url;

        if (preg_match('~!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)~', $url, $m) || preg_match('~@(-?\d+\.\d+),(-?\d+\.\d+)~', $url, $m)) {
            return [(float) $m[1], (float) $m[2]];
        }

        return null;
    }

    /**
     * Alamat peta untuk iframe, berurutan:
     * 1) Kode sematan Google Maps (nama tempat tampil), hanya bila berasal dari Google.
     * 2) Nama tempat dari tautan Google Maps (+ koordinat bila ada): nama dan pin tampil seperti di Google Maps.
     * 3) Koordinat saja (hanya pin, tanpa nama).
     * 4) Tanpa keduanya: pencarian nama + alamat.
     */
    public function mapEmbedUrl(): ?string
    {
        $embed = (string) $this->maps_embed_url;

        if (str_starts_with($embed, 'https://www.google.com/maps/embed')) {
            return $embed;
        }

        // ID tempat Google (CID) dari tautan: cara paling tepat agar kartu info dan pin tempatnya tampil.
        if ($cid = $this->googleCid()) {
            return 'https://maps.google.com/maps?cid=' . $cid . '&z=17&output=embed';
        }

        $coords = $this->coordinates();
        $ll = $coords ? $coords[0] . ',' . $coords[1] : null;

        if ($place = $this->googlePlaceName()) {
            return 'https://maps.google.com/maps?q=' . rawurlencode($place) . ($ll ? '&ll=' . $ll : '') . '&z=17&output=embed';
        }

        if ($ll) {
            return 'https://maps.google.com/maps?q=' . $ll . '&z=16&output=embed';
        }

        $query = trim($this->name . ' ' . $this->address . ' ' . $this->city);

        return $query === '' ? null : 'https://maps.google.com/maps?q=' . rawurlencode($query) . '&z=16&output=embed';
    }

    /** True bila nama tempat tampil dari Google sendiri (maka label nama buatan situs disembunyikan). */
    public function hasGoogleEmbed(): bool
    {
        return str_starts_with((string) $this->maps_embed_url, 'https://www.google.com/maps/embed')
            || $this->googleCid() !== null
            || $this->googlePlaceName() !== null;
    }

    public function openStatus(): ?string
    {
        if (! $this->opens_at || ! $this->closes_at) {
            return null;
        }

        $now = now()->format('H:i:s');
        $opens = $this->opens_at;
        $closes = $this->closes_at;

        $isOpen = $opens <= $closes
            ? ($now >= $opens && $now < $closes)
            : ($now >= $opens || $now < $closes);

        $fmt = fn (string $time): string => substr($time, 0, 2) . '.' . substr($time, 3, 2);

        return $isOpen ? 'Buka sampai ' . $fmt($closes) : 'Tutup, buka lagi ' . $fmt($opens);
    }
}