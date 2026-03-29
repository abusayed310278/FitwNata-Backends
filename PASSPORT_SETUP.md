# Laravel Passport Setup Guide for FitwNata API

## 1. Install Laravel Passport

```bash
composer require laravel/passport
```

## 2. Run Passport Migrations

```bash
php artisan migrate
```

## 3. Install Passport

```bash
php artisan passport:install
```

This command will:
- Create the encryption keys needed to generate secure access tokens
- Create "personal access" and "password grant" clients

## 4. Update your .env file

Add the client credentials from the install command output:

```env
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=client-id-value
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=unhashed-client-secret-value
```

## 5. API Routes

The API routes are already configured in `routes/api.php` with:
- Public routes for registration, login, social login
- Protected routes using `auth:api` middleware
- Comprehensive endpoints for all app modules

## 6. API Endpoints Overview

### Authentication
- `POST /api/v1/register` - User registration
- `POST /api/v1/login` - User login
- `POST /api/v1/social-login` - Social media login
- `POST /api/v1/logout` - Logout (protected)
- `POST /api/v1/refresh-token` - Refresh access token (protected)

### User Onboarding
- `GET /api/v1/onboarding/questions` - Get questionnaire
- `POST /api/v1/onboarding/submit` - Submit answers
- `GET /api/v1/onboarding/recommendations` - Get recommendations
- `POST /api/v1/onboarding/complete` - Complete onboarding

### User Profile
- `GET /api/v1/profile` - Get user profile
- `PUT /api/v1/profile` - Update profile
- `POST /api/v1/profile/avatar` - Upload avatar

### Subscriptions
- `GET /api/v1/plans` - Get available plans (public)
- `GET /api/v1/subscription` - Get current subscription
- `POST /api/v1/subscription/subscribe` - Subscribe to plan
- `POST /api/v1/subscription/cancel` - Cancel subscription

### Workouts
- `GET /api/v1/workouts` - Get workouts with filtering
- `GET /api/v1/workouts/recommended` - Get recommended workouts
- `GET /api/v1/workouts/{workout}` - Get workout details
- `POST /api/v1/workouts/{workout}/complete` - Mark workout complete

### Nutrition
- `GET /api/v1/nutrition/meal-plans` - Get meal plans
- `GET /api/v1/nutrition/recipes` - Get recipes
- `GET /api/v1/nutrition/weekly-plan` - Get weekly meal plan

### Appointments
- `GET /api/v1/appointments` - Get user appointments
- `POST /api/v1/appointments` - Book appointment
- `GET /api/v1/appointments/coaches` - Get available coaches
- `GET /api/v1/appointments/upcoming` - Get upcoming appointments

### E-commerce
- `GET /api/v1/shop/products` - Get products
- `POST /api/v1/shop/cart/add` - Add to cart
- `GET /api/v1/shop/cart` - Get cart
- `POST /api/v1/shop/checkout` - Checkout
- `GET /api/v1/shop/orders` - Get user orders

### Progress Tracking
- `GET /api/v1/progress/dashboard` - Progress dashboard
- `GET /api/v1/progress/measurements` - Get measurements
- `POST /api/v1/progress/measurements` - Add measurement
- `GET /api/v1/progress/journal` - Get journal entries
- `POST /api/v1/progress/journal` - Add journal entry

### Content
- `GET /api/v1/content/featured` - Featured content (public)
- `GET /api/v1/content/articles` - Get articles
- `GET /api/v1/content/videos` - Get videos
- `GET /api/v1/content/tips` - Get fitness tips

## 7. Authentication Usage

### Request Headers
```
Authorization: Bearer {access_token}
Content-Type: application/json
Accept: application/json
```

### Sample Login Response
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "token_type": "Bearer",
        "expires_at": "2024-01-15T12:00:00.000000Z",
        "onboarding_completed": false
    }
}
```

## 8. Rate Limiting

API endpoints include built-in rate limiting:
- Authentication endpoints: 5 attempts per minute
- General API endpoints: 60 requests per minute per user

## 9. Error Handling

All endpoints return consistent JSON responses:

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {}
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {}
}
```

## 10. Testing

Use tools like Postman or Insomnia to test the API endpoints. Start with:

1. Register a new user: `POST /api/v1/register`
2. Get access token from response
3. Use token to access protected endpoints

## 11. Next Steps

1. Install Passport using the commands above
2. Set up your mobile app to consume these APIs
3. Implement Stripe integration for actual payments
4. Add push notifications for appointments and orders
5. Implement file uploads for workout videos and recipe images

The API is now ready for mobile app integration with comprehensive functionality for the FitwNata fitness platform!
