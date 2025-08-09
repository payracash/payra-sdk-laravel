# Payra Laravel SDK (Backend Signature Generation)

Official **Laravel SDK** for integrating **Payra's on-chain payment system** into your backend.
Provides a simple way to generate secure **ECDSA** signatures compatible with the Payra smart contract (e.g. for payment verification).

---

## Features

- Ethereum ECDSA signature generation using the  `secp256k1`  curve
- Fully compatible with Payra's Solidity contracts (ERC-1155 payment verification)
-   Built-in ABI encoding via  `web3.php`
-   Multi-network support via  `.env`  and  `config/payra.php`
-   Laravel IoC container integration (easy dependency injection)

---

## Setup

Before installing this package, make sure you have an active Payra account:

- [https://payra.cash](https://payra.cash)

You will need your merchantID and a dedicated account (private key) to generate valid payment signatures.

---

## Requirements

-   Laravel 8+
-   PHP 8.0+
-   Payra account with  `merchantId`  and  `privateKey`  for the selected network
    -   [https://payra.cash](https://payra.cash/)
---

## Installation

### Via Composer (recommended)

```
composer require payracash/payra-sdk-laravel
```
Laravel will auto-discover the service provider.  
You can also publish the configuration file:
```
php artisan vendor:publish --tag=payra-config
```

---

### Environment Setup

In your `.env` file, set the credentials for the networks you plan to use:

```
PAYRA_POLYGON_PRIVATE_KEY=your_private_key_here
PAYRA_POLYGON_MERCHANT_ID=your_merchant_id_here

PAYRA_ETHEREUM_PRIVATE_KEY=
PAYRA_ETHEREUM_MERCHANT_ID=

PAYRA_LINEA_PRIVATE_KEY=
PAYRA_LINEA_MERCHANT_ID=
```

These values will be loaded into `config/payra.php`.

---

## Usage Example

```php
use Payra\Laravel\PayraSignatureGenerator;

class PaymentController extends Controller
{
    public function sign()
    {
        $generator = app(PayraSignatureGenerator::class);

        $signature = $generator->generateSignature(
            'polygon',         // Network
            '0xTokenAddress',  // ERC-20 token address (e.g. USDT)
            'order_12345',     // Unique order ID per merchant
            1_000_000,         // Amount in Wei ($1 = 1_000_000)
            now()->timestamp,  // Timestamp
            '0xPayerAddress'   // Payer's wallet address
        );

        return response()->json(['signature' => $signature]);
    }
}
```
You can also inject the generator via constructor:

```
public function __construct(private PayraSignatureGenerator $payra) {}

public function signOrder()
{
    return $this->payra->generateSignature(...);
}
```

---

## Security Notice


-   **Never**  expose your  `privateKey`  in frontend code or public repositories    
-   This SDK is  **server-side only**
-   Consider using Laravel's configuration caching or a secrets manager for sensitive keys
---

## Project

-   [https://payra.cash](https://payra.cash)
-   [https://payra.tech](https://payra.tech)
-   [https://payra.xyz](https://payra.xyz)
-   [https://payra.eth](https://payra.eth)

---

## Social Media

- [Telegram Payra Group](https://t.me/+GhTyJJrd4SMyMDA0)
- [Telegram Announcements](https://t.me/payracash)
- [Twix (X)](https://x.com/PayraCash)

---

##  License

MIT © [Payra](https://github.com/payracash)
