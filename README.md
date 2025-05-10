# 📡 Mock SMS Server

A mock SMS gateway built with Laravel to simulates sending and delivering SMS messages with status updates and webhook notifications — for local and staging environments.

---

## 🚀 Features

- 📤 Simulate message sending (single or bulk)
- 📦 Message statuses: `queued`, `sent`, `delivered`, `failed`
- 🔁 Automatic status progression via jobs
- 📡 Webhook dispatching for each status update
- 🔐 Simple Bearer token authentication (`test-key`)
- 🧪 Supports JSON and form-data requests
- ⏳ Intelligent webhook polling (up to 24 hours)

---



## 📦 Installation

Clone the repository

```bash
git clone git@github.com:levintoo/mock-sms-server.git
cd mock-sms-server
````

Install PHP dependencies
```bash
composer install
````

Copy environment file and generate app key
```bash
cp .env.example .env
php artisan key:generate
````

(Required) Set your webhook endpoint in .env
```bash
SMS_WEBHOOK=
````

Run database migrations
```bash
php artisan migrate
````

Start a queue worker
```bash
php artisan queue:work --tries=3 --backoff=3
````

---

## 🔐 Authentication

Use a fixed Bearer token for all requests:

```
Authorization: Bearer test-key
```

---

## 📤 Sending Messages

### Endpoint

```
POST /api/message
```

### Request Formats

#### ✅ JSON (application/json)

```json
{
  "to": "254700123123", // or ["254700123123", "254711456789"] for queued bulk
  "message": "Hello world!"
}
```

#### ✅ Form-Data (multipart/form-data)

```json
to: "254700123123",
// or for queued bulk
// to[] = 254700123123
// to[] = 254711456789
message = "Hello world!"
```

### Validation Rules

| Field     | Type            | Description                             |
| --------- | --------------- | --------------------------------------- |
| `to`      | string or array | International phone number(s), required |
| `message` | string          | Message body, required                  |

---

## 📬 Message Status Lifecycle

Messages transition through the following statuses:

```php
enum MessageStatus: string {
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Failed = 'failed';
}
```

---

## 📡 Webhook Configuration

Set your webhook URL in `.env`:

```
SMS_WEBHOOK=https://your-app.test/webhook
```

### Webhook Payload

```json
{
    'event' => ...,
    'data' => [
        ...
    ],
}
```

---

## 🔁 Webhook Retry Strategy

* ⏱ **Every 5 seconds** for 1 minute
* ⏱ **Every 1 minute** for 5 minutes
* ⏱ **Every 5 minutes** for 55 minutes
* ⏱ **Every hour** until 24 hours total
* ❌ **Stop retrying** after 24 hours

Polling stops once a `200 OK` response is received.

---

## 🧵 Job Classes

| Job Class                | Description                                  |
| ------------------------ | -------------------------------------------- |
| `MockMessageSendJob`     | Simulates transition to `sent`               |
| `MockMessageDeliveryJob` | Simulates transition to `delivered`/`failed` |
| `PollDeliveryWebhookJob` | Polls the webhook URL                        |

---
