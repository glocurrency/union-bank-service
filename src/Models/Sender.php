<?php

namespace GloCurrency\UnionBank\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use GloCurrency\UnionBank\Database\Factories\SenderFactory;
use BrokeYourBike\UnionBank\Interfaces\SenderInterface;
use BrokeYourBike\CountryCasts\Alpha2Cast;
use BrokeYourBike\BaseModels\BaseUuid;

/**
 * GloCurrency\UnionBank\Models\Sender
 *
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $country_code
 * @property string $country_code_alpha2
 * @property string $phone_number
 * @property string $bank_account
 * @property string $bank_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Sender extends BaseUuid implements SenderInterface
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'union_bank_senders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<mixed>
     */
    protected $casts = [
        'country_code_alpha2' => Alpha2Cast::class . ':country_code',
    ];

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getBankAccount(): string
    {
        return $this->bank_account;
    }

    public function getBankCode(): string
    {
        return $this->bank_code;
    }

    public function getCountryCode(): string
    {
        return $this->country_code_alpha2;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SenderFactory::new();
    }
}
