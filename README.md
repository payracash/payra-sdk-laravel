# Payra Laravel SDK

Official **Laravel SDK** for integrating **Payra's on-chain payment system** into your backend applications.

This SDK provides:
- Secure generation of **ECDSA signatures** compatible with the Payra smart contract — used for order payment verification.
- Simple methods for **checking the on-chain status of orders** to confirm completed payments.

## How It Works

The typical flow for signing and verifying a Payra transaction:

1.  The **frontend** prepares all required payment parameters:
    -  **Network** – blockchain name (e.g. Polygon, Linea)
    -  **Token address** – ERC-20 token contract address
    -  **Order ID** – unique order identifier
    -  **Amount Wei** – already converted to the smallest unit (e.g. wei, 10⁶)
    -  **Timestamp** – Unix timestamp of the order
    -  **Payer wallet address** – the wallet address from which the user will make the on-chain payment
2.  The frontend sends these parameters to your **backend**.
3.  The **backend** uses this SDK to generate a cryptographic **ECDSA signature** with its private key (performed **offline**).
4.  The backend returns the generated signature to the frontend.
5.  The **frontend** calls the Payra smart contract (`payOrder`) with all parameters **plus** the signature.

This process ensures full compatibility between your backend and Payra’s on-chain verification logic.

## Features

- Generates **Ethereum ECDSA signatures** using the `secp256k1` curve.
- Fully compatible with **Payra's Solidity smart contracts** (`ERC-1155` payment verification).  
- Includes built-in **ABI encoding and decoding** via `web3.php`.
- Supports `.env` and  `config/payra.php` configuration for multiple blockchain networks.  
- Laravel IoC container integration (easy dependency injection)
- Verifies **order payment status directly on-chain** via RPC or blockchain explorer API.  
- Provides **secure backend integration** for signing and verifying transactions.
- Includes optional utility helpers for:
  - **Currency conversion** (via [ExchangeRate API](https://www.exchangerate-api.com/))
  - **USD ⇄ WEI** conversion for token precision handling.  

## Setup

Before installing this package, make sure you have a **MerchantID**

- [https://payra.cash/products/on-chain-payments/merchant-registration](https://payra.cash/products/on-chain-payments/merchant-registration)

You will need:
- Your **Merchant ID** (unique for each blockchain network)
- Your **private key** (used to sign Payra transactions securely)

Additionally:
- Create a free account at [QuickNode](https://www.quicknode.com/) to obtain your **RPC URLs** — these are required for reading on-chain order status directly from the blockchain.

Optional (recommended):
- Create a free API key at [ExchangeRate API](https://www.exchangerate-api.com/)  
  if you want to enable **automatic fiat → USD** conversions using the built-in utilities.

## Installation

### Requirements

-   Laravel 8+
-   PHP 8.0+
-   Payra account with  `merchantId`  and  `privateKey`  (available in the Payra dashboard) for the selected network
    -   [https://payra.cash](https://payra.cash/)

### Via Composer (recommended)

```
composer require payracash/payra-sdk-laravel
```
Laravel will auto-discover the service provider.  
You can also publish the configuration file:
```
php artisan vendor:publish --tag=payra-config
```

### Environment Setup

In your `.env` file, set the credentials for the networks you plan to use:

```bash
# (Optional) API key required for authenticating backend requests
PAYRA_API_KEY=

# Optional: used by PayraUtils::convertToUSD() for fiat conversions
PAYRA_EXCHANGE_RATE_API_KEY=
PAYRA_EXCHANGE_RATE_CACHE_TIME=720 # in minutes

# Polygon network configuration
PAYRA_POLYGON_MERCHANT_ID=
PAYRA_POLYGON_CORE_FORWARD_CONTRACT_ADDRESS=0xf30070da76B55E5cB5750517E4DECBD6Cc5ce5a8
PAYRA_POLYGON_PRIVATE_KEY=
PAYRA_POLYGON_RPC_URL_1=

# Ethereum network configuration
PAYRA_ETHEREUM_CORE_FORWARD_CONTRACT_ADDRESS=
PAYRA_ETHEREUM_PRIVATE_KEY=
PAYRA_ETHEREUM_MERCHANT_ID=
PAYRA_ETHEREUM_RPC_URL_1=
PAYRA_ETHEREUM_RPC_URL_2=

# Linea network configuration
PAYRA_LINEA_CORE_FORWARD_CONTRACT_ADDRESS=
PAYRA_LINEA_PRIVATE_KEY=
PAYRA_LINEA_MERCHANT_ID=
PAYRA_LINEA_RPC_URL_1=
PAYRA_LINEA_RPC_URL_2=
```

These values will be loaded into `config/payra.php`.

#### Important Notes

-   The cache automatically refreshes when it expires.    
-   You can adjust the cache duration by setting  `PAYRA_EXCHANGE_RATE_CACHE_TIME`:
    -   `5`  → cache for 5 minutes
    -   `60`  → cache for 1 hour
    -   `720`  → cache for 12 hours (default)
- Each network (Polygon, Ethereum, Linea) has its own  **MERCHANT_ID**,  **PRIVATE_KEY**, and  **RPC URLs**.  
- The SDK automatically detects which chain configuration to use based on the selected network.
- You can use multiple RPC URLs for redundancy (the SDK will automatically fall back if one fails).
- Contract addresses correspond to the deployed Payra Core Forward contracts per network.

### Payra API key (server-to-server)

This package uses a simple API key to protect backend HTTP endpoints (for example  `/api/payra/sign`  and  `/api/payra/convert/to/usd`).  
**You create this key yourself**  and keep it secret — it is  **not**  issued by Payra. The backend (your Laravel app) validates the key in the  `X-Payra-Key`  header.

> **Note:** The API key is only required if you expose Payra endpoints via HTTP (for example, when your frontend calls your backend).
> If you are using the SDK internally in your Laravel app or services (without calling API routes), you can safely omit this variable.

#### Why do we use it?

-   It prevents unauthorized clients from calling your Payra endpoints.
-   It’s a lightweight server-to-server authentication mechanism for internal integrations.

#### How to generate a secure key

Generate a long random key (do not handcraft a short password). Examples:

```bash
# recommended: 32 bytes hex
php -r "echo bin2hex(random_bytes(32));"
# or using openssl
openssl rand -hex 32
```

Put the generated key into your  `.env`:

```bash
# API key required for authenticating backend requests
PAYRA_API_KEY=your_generated_hex_key_here
```

#### How to call the API (example with curl / Postman)

Include the key in the  `X-Payra-Key`  header:

```bash
curl -X POST "https://your-domain.com/api/payra/sign" \
  -H "Content-Type: application/json" \
  -H "X-Payra-Key: your_generated_hex_key_here" \
  -d '{"network":"polygon","token_address":"0x...","order_id":"order_1","amount":"1000000","timestamp":1234567890,"payer_address":"0x..."}'
```

#### Security best practices

-   Always use  **HTTPS**  — never send the key over plain HTTP.
-   Store the key only in server-side  `.env`  (never in client-side code or public repositories).
-   Consider rotating the key periodically and update  `.env`  (and deployed configs).
-   If you need stricter security, combine the API key with IP allowlists, HMAC signatures, or short-lived tokens.
-   If  `PAYRA_API_KEY`  is missing from  `.env`, middleware will return an explicit configuration error (500); if it is present but mismatched, requests return  `401 Unauthorized`.

## Usage Example

### Generate Signature

```php
use Payra\PayraSignatureGenerator;

class PayraController extends Controller
{
    public function generateSignature()
    {
        $payraSignature = app(PayraSignatureGenerator::class);

        $signature = $payraSignature->generateSignature(
            $network,         	// e.g. "polygon"
            $tokenAddress,    	// ERC-20 USDT or USDC
            $orderId,         	// string (unique per merchantId)
            $amountWei,        	// in Wei $1 = 1_000_000
            (int) $timestamp,	// now()->timestamp
            $payerAddress     	// Public payer wallet address
        );

        return response()->json(['signature' => $signature]);
    }
}
```

#### Response

```php
{
	"status": "success",
	"signature": "0x2772922237f8960627760c568965903c9ea25f76bd63320fc6c03bc8f614905036837347c8ed7e94b93c570ebe5e6abb0272b3096fce065e000d5413a8c3561c1c",
	"message": "Signature generated successfully."
}
```

#### Input Parameters

| Field         | Type     | Description                                  |
|--------------|----------|----------------------------------------------|
| **`network`**    | `string` | Selected network name                        |
| **`tokenAddress`** | `string` | ERC20 token contract address                 |
| **`orderId`**     | `string` | Unique order reference (e.g. ORDER-123)      |
| **`amountWei`**      | `string` or `integer` | Token amount in smallest unit (e.g. wei)     |
| **`timestamp`**   | `number` | Unix timestamp of signature creation         |
| **`payerAddress`**   | `string` | Payer Wallet Address

---

### Get Order Status

Retrieve **full payment details** for a specific order from the Payra smart contract. This method returns the complete on-chain payment data associated with the order, including:

-   whether the order has been paid,
-   the payment token address,
-   the paid amount,
-   the fee amount,
-   and the payment timestamp.

Use this method when you need  **detailed information**  about the payment or want to display full transaction data.

```php
use Payra\PayraOrderVerification;

class PayraController extends Controller
{
    public function orderVerification()
    {
	    $orderVerification = app(PayraOrderVerification::class);

        $orderStatus = $orderVerification->getOrderStatus(
	        $network,         	// e.g. "polygon"
			$orderId,         	// string (unique per merchantId)
		);

		return response()->json(['result' => $orderStatus]);     
	}
}
```

#### Response

```php
{
    "result": {
        "success": true,
        "error": null,
        "paid": true,
        "token": "0xc2132d05d31c914a87c6611c10748aeb04b58e8f",
        "amount": 400000,
        "fee": 3600,
        "timestamp": 1765138941
    }
}
```

---

### Check Order Paid Status

Perform a  **simple payment check**  for a specific order. This method only verifies whether the order has been paid (`true`  or  `false`) and does  **not**  return any additional payment details.

Use this method when you only need a  **quick boolean confirmation**  of the payment status.

```php
use Payra\PayraOrderVerification;

class PayraController extends Controller
{
    public function orderVerification()
    {
	    $orderVerification = app(PayraOrderVerification::class);

        $verify = $orderVerification->isOrderPaid(
	        $network,         	// e.g. "polygon"
			$orderId,         	// string (unique per merchantId)
		);

		return response()->json(['result' => $verify]);     
	}
}
```

#### Response

```php
{
    "result": {
        "success": true,
        "paid": true,
        "error": null
    }
}
```

## Utilities / Conversion Helpers

The SDK includes  **helper functions**  for working with token amounts and currency conversion.

### 1. Get Token Decimals

```php
use Payra\PayraUtils;

class PaymentController extends Controller
{
	public function convertToUSD()
	{
		$utils = app(PayraUtils::class);
	    $tokenDecimals = $utils->getTokenDecimals('polygon', 'USDT');

		return response()->json(['token_decimals' => $tokenDecimals]);
	}
}
```

#### Response

```php
{
    "token_decimals": 6
}
```

Returns the number of decimal places for a given token on a specific network.

---

### 2. Convert USD/Token Amounts to Wei

```php
use Payra\PayraUtils;

class PaymentController extends Controller
{
	public function convertToUSD()
	{
		$utils = app(PayraUtils::class);
	    $toWei = $utils->toWei(14.33, 'polygon', 'USDT');

		return response()->json(['to_wei' => $toWei]);
	}
}
```

#### Response

```php
{
    "to_wei": 14330000
}
```

---

### 3. Convert Wei to USD/Token

```php
use Payra\PayraUtils;

class PaymentController extends Controller
{
	public function convertToUSD()
	{
		$utils = app(PayraUtils::class);
	    $fromWei = $utils->fromWei(34345230, 'polygon', 'USDT');

		return response()->json(['from_wei' => $fromWei]);
	}
}
```

#### Response

```php
{
    "from_wei": 34.35
}
```

### 4. Currency Conversion (Optional)

Payra processes all payments in  **USD**.  If your store uses another currency (like EUR, AUD, or GBP), you can:

-   Convert the amount to USD on your backend manually,  **or**
-   Use the built-in helper provided in the SDK.

```php
use Payra\PayraUtils;

class PaymentController extends Controller
{
	public function convertToUSD()
	{
		$utils = app(PayraUtils::class);

		$convertedAmount = $utils->convertToUSD(
			$amount,		// e.g. 232.23
			$from_currency	// e.g. EUR
		);

		return response()->json(['converted_amount' => $convertedAmount]);
	}
}
```

#### Response

```php
{
    "converted_amount": 177.64 # in USD
}
```

#### Setup for Currency Conversion

To use the conversion helper, you need a free API key from  **[exchangerate-api.com](https://exchangerate-api.com/)**.

1.  Register a free account and get your API key.
2.  Add the key to your  `.env`  file:

```php
PAYRA_EXCHANGE_RATE_API_KEY=your_api_key_here
```

4.  That’s it — Payra will automatically fetch the exchange rate and calculate the USD amount.

**Note:** The free plan allows 1,500 requests per month, which is sufficient for most stores. Exchange rates on this plan are updated every 24 hours, so with caching, it’s more than enough. Paid plans offer faster update intervals.

---

#### You can also inject the generator via constructor:

```php
use Payra\PayraSignatureGenerator;
use Payra\PayraOrderVerification;
use Payra\PayraUtils;

class PaymentController extends Controller
{
    public function __construct(
        private PayraSignatureGenerator $payraSignature,
        private PayraOrderVerification $orderVerification,
        private PayraUtils $payraUtils
    ) {}

    public function generateSignature()
    {
        $signature = $this->payraSignature->generateSignature(
            $network,         // e.g. "polygon"
            $tokenAddress,    // ERC-20 USDT or USDC
            $orderId,         // string (unique per merchantId)
            $amountWei,       // in Wei $1 = 1_000_000
            (int) $timestamp, // now()->timestamp
            $payerAddress     // Public payer wallet address
        );

        return response()->json([
            'status' => 'success',
            'signature' => $signature,
            'message' => 'Signature generated successfully.'
        ]);
    }

    public function orderVerification()
    {
        $orderStatus = $this->orderVerification->isOrderPaid(
            $network,   // e.g. "polygon"
            $orderId    // string (unique per merchantId)
        );

        return response()->json(['order_status' => $orderStatus]);
    }

    public function convertToUSD()
    {
        $convertedAmount = $this->payraUtils->convertToUSD(
            $amount,        // e.g. 232.23
            $from_currency  // e.g. EUR
        );

        return response()->json(['converted_amount' => $convertedAmount]);
    }
}
```

## Security Notice

Never expose your private key in frontend or client-side code.  
This SDK is  **server-side only**  and must be used securely on your backend. Never use it in frontend or browser environments. Also, never commit your `.env`  file to version control.

## Project

-   [https://payra.cash](https://payra.cash)
-   [https://payra.tech](https://payra.tech)
-   [https://payra.xyz](https://payra.xyz)
-   [https://payra.eth](https://payra.eth.limo) - suporrted by Brave Browser or .limo

## Social Media

- [Telegram Payra Group](https://t.me/+GhTyJJrd4SMyMDA0)
- [Telegram Announcements](https://t.me/payracash)
- [Twix (X)](https://x.com/PayraCash)
- [Dev.to](https://dev.to/payracash)

##  License

MIT © [Payra](https://payra.cash)
