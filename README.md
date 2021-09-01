# Laravel Scoring

<p align="center">
    <a href="https://github.styleci.io/repos/402087252"><img src="https://github.styleci.io/repos/402087252/shield?style=flat" alt="StyleCI Status"></a>
    <a href="https://packagist.org/packages/arttiger/laravel-scoring"><img src="https://img.shields.io/packagist/dt/arttiger/laravel-scoring?style=flat" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/arttiger/laravel-scoring"><img src="https://img.shields.io/packagist/v/arttiger/laravel-scoring?style=flat" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/arttiger/laravel-scoring"><img src="https://img.shields.io/packagist/l/arttiger/laravel-scoring?style=flat" alt="License"></a>
</p>

Integration of scoring systems for Laravel. The package provides integration scoring systems:
- RiskTools

## Installation

Install the package via composer:

```
composer require arttiger/laravel-scoring
```

### Configuration

In order to edit the default configuration you may execute:

```
php artisan vendor:publish --provider="Arttiger\Scoring\ScoringServiceProvider" --tag="config"
```

After that, `config/scoring.php` will be created.

### Environment

Set environment variable (`.env`)

```
RISK_TOOLS_URL=
RISK_TOOLS_KEY=
```

## Usage

#### Get scoring data

You can get data from the scoring system as follows:

```
$result = Scoring::scoring('risk_tools', $attributes);
```

- 'risk_tools' - scoring system;
- $attributes - data (array) from the credit model according to the mapping of variables in `config/scoring.php`. This map establishes the correspondence between the attributes of your model and the required query fields in scoring system.

```
'model_data' => [
    // For "scoring" method
    'id'              => 'public_id',     // ID of loan
    'user_id'         => 'client_id',     // ID of client
    'social_number'   => 'inn',           // INN of client
    'phone'           => 'phone',         // Phone of client
    'email'           => 'email',         // Email of client
    'passport_number' => 'passport',      // Passport (example АА123456 or number ID-card)
    'passport_date'   => 'passport_date', // Passport issue date (example "2001-12-01")
    'first_name'      => 'firstName',     // First name
    'last_name'       => 'lastName',      // Last name
    'other_name'      => 'middleName',    // Middle name
    'birth_date'      => 'birth_date',    // Birth date (example "2001-12-01")
    'gender_id'       => 'gender_id',     // Gender (1 - male, 2 - female)
    'loan_amount'     => 'sum',           // Amount of credit
    'loan_days'       => 'period',        // Number days of credit
    'applied_at'      => 'created_at',    // Date and time created loan (example "2018-04-05T19:29:51+03:00")
    'ip'              => 'ip',            // IP of client
    'user_agent'      => 'user_agent',    // User-Agent of browser of client
    'ubki'            => 'ubki',          // XML-data from UBKI

    // For updating of statuses
    'status_id'       => 'status_code',   // Status code of credit (1 - NEW, 2 - REJECT, 3 - APPROVED, 4 - ISSUED, 5 - CLOSED, 6 - OVERDUE)
    'closed_at'       => 'closed_at',     // Date and time close loan (example "2018-04-25T10:05:00+03:00")
    'amount_to_pay'   => 'amount_debt',   // Total debt
    'total_paid'      => 'total_paid',    // The sum of all payments
    'overdue_days'    => 'overdue_days',  // The total number of days of delay of loan
],
```

`$result` - response from scoring system (array).

```
$result = [
    "group" => 4,
    "score" => 0.495,
    "amount_limit_max" => 1200,
    "amount_limit" => 1000.0,
    "filters" => [],
    "score_model" => [
        "version" => "v1_7"
    ]
]
```

#### Send status of credit

Send status of credit to the scoring system as follows:

```
$result = Scoring::status('risk_tools', $attributes);
```

- 'risk_tools' - scoring system;
- $attributes - data (array) from the credit model according to the mapping of variables

#### Get the UBKI reports

Get the UBKI reports from the scoring system as follows:

```
$result = Scoring::ubkiReport('risk_tools', ['public_id' => $public_id]);
```

- $public_id - ID of credit

```
$result = [
    "reports": [
        [
            "report_date" => "2018-12-25",
            "xml" => "<xml>"
        ],
        [
            "report_date" => "2018-12-25",
            "xml" => "<xml>"
        ]
    ]
]
```

#### Get pre-scoring data

You can get data from the pre-scoring system as follows:

```
$result = Scoring::pre_scoring('risk_tools', ['social_number' => $social_number]);
```

- $social_number - Social number (INN) of client

#### Trait

Add `ScoringTrait`-trait to the model with client data:

```
    use Arttiger\Scoring\Traits\ScoringTrait;

    class Loan extends Model
    {
        use ScoringTrait;
        ...
    }
```

Add a new method `scoringAttributes()` to the class to add the necessary attributes and fill them with data:

```
    use Arttiger\Scoring\Traits\ScoringTrait;

    class Loan extends Model
    {
        use ScoringTrait;
        ...
        
        public function scoringAttributes()
        {
            $client_data = json_decode($this->attributes['client_data']);
            $this->attributes['client_id']  = trim($client_data->id); 
            $this->attributes['inn']        = trim($client_data->code); 
            $this->attributes['lastName']   = trim($client_data->lastName); 
            ...
        }
    }
```

You can use other ways to create custom attributes that you specified in `'model_data'` (`config/scoring.php`).

### Change log

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](CONTRIBUTING.md) for details and a todolist.

### Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

[Volodymyr Farylevych](https://github.com/arttiger)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

