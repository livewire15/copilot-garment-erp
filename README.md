# Rishub Handicraft ERP

A production-quality ERP system for Rishub Handicraft - The Bakhiya Stories garment manufacturing business.

## Features

- **Dashboard**: Overview of key metrics
- **Products Management**: Add, edit, delete products with color variants and inventory
- **Invoice Management**: Create, manage, and track invoices with payment history
- **Inventory Management**: Automatic inventory tracking and stock management
- **Payment Tracking**: Track payments with multiple payment modes

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Bootstrap 5
- Blade Templates

## Hosting

Designed for Hostinger Premium Shared Hosting with no Docker, Redis, or Queue Workers.

## Setup

```bash
# Clone repository
git clone https://github.com/livewire15/copilot-garment-erp.git
cd copilot-garment-erp

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Create symbolic link for storage
php artisan storage:link

# Start development server
php artisan serve
```

## Project Structure

```
app/
├── Models/              # Eloquent models
├── Http/
│   ├── Controllers/     # Controllers
│   └── Requests/        # Form validation requests
database/
├── migrations/          # Database migrations
└── seeders/            # Database seeders
resources/
├── views/              # Blade templates
└── css/
routes/
├── web.php             # Web routes
storage/
├── app/
│   └── public/
│       └── products/   # Product images
```

## Database Tables

- `users` - Admin users
- `products` - Product details
- `product_variants` - Color variants
- `product_variant_inventory` - Size-wise inventory
- `customers` - Customer information
- `invoices` - Invoice records
- `invoice_items` - Line items in invoices
- `payments` - Payment records

## License

Private
