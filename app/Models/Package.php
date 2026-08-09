<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'audio_path',
        // 'duration',
        'status',
        'total_questions',
        'secret_token'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    protected $guarded = [];

    protected static function booted()
    {
        // EVENT CREATING: Generate Secret 8 Digit Hex (Contoh: A1B2C3D4)
        static::creating(function ($package) {
            if (empty($package->token_secret)) {
                // random_bytes(4) = 32 bit = 8 karakter hex
                $package->token_secret = strtoupper(bin2hex(random_bytes(4)));
            }
        });

        // EVENT SAVED: Update jumlah soal (Logic lama Anda)
        static::saved(function ($package) {
            $package->updateQuietly([
                'total_questions' => $package->questions()->count(),
            ]);
        });
    }

    // --- LOGIC TOKEN ---

    public function getCurrentTokenAttribute()
    {
        return $this->generateTokenByTime(now()->timestamp);
    }

    public function generateTokenByTime($timestamp)
    {
        if (! $this->token_secret) {
            return null;
        }

        // Blok waktu 30 menit (1800 detik)
        $timeBlock = floor($timestamp / 1800);

        // Gabung ID + Secret + TimeBlock -> Hash MD5 -> Ambil 8 Char -> Uppercase
        return strtoupper(substr(md5($this->id.$this->token_secret.$timeBlock), 0, 8));
    }

    public function isValidToken($inputToken)
    {
        if (! $this->token_secret) {
            return true;
        } // Gratis/Public
        $input = strtoupper($inputToken);

        // Cek Token Sekarang & Token 30 Menit lalu (Toleransi)
        return $input === $this->generateTokenByTime(now()->timestamp) ||
               $input === $this->generateTokenByTime(now()->subMinutes(30)->timestamp);
    }

    // Semua audio yang terkait
    public function audios()
    {
        return $this->hasMany(Audio::class);
    }

    // Audio aktif
    public function activeAudio()
    {
        return $this->hasOne(Audio::class)->where('is_active', 1);
    }

    public function passages()
    {
        return $this->hasMany(Passage::class);
    }

    // Instruksi ujian yang terkait
    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    public function getInstruction(string $section, ?string $part = null)
    {
        return $this->instructions()
            ->where('section', $section)
            ->when($part, fn ($q) => $q->where('part', $part))
            ->orderBy('order')
            ->get();
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }
    
    // Pastikan relationship ke questions juga sudah ada (biasanya sudah)
   

    

    
    

    
}
