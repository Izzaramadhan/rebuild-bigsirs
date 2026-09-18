# Authentication API

BigSIRS uses Laravel Sanctum SPA Authentication (Cookie-Based). It does NOT use bearer tokens.

## Endpoints and Request Flow

The login flow must follow this exact sequence:

1. `GET /sanctum/csrf-cookie` (Initialize CSRF protection)
2. `GET /captcha` (Fetch CAPTCHA challenge)
3. `POST /login` (Authenticate with credentials)
4. `GET /api/user` (Fetch current user data)

### 1. CSRF Cookie
- **Endpoint**: `/sanctum/csrf-cookie`
- **Method**: `GET`
- **Purpose**: Sets the `XSRF-TOKEN` cookie required for stateful requests.

### 2. Fetch CAPTCHA
- **Endpoint**: `/captcha`
- **Method**: `GET`
- **Response**:
```json
{
  "success": true,
  "data": {
    "image": "data:image/svg+xml;base64,...",
    "expires_in": 300
  }
}
```
- **Notes**:
  - CAPTCHAs are valid for 5 minutes (`expires_in` seconds).
  - CAPTCHA validation occurs on the server. The answer is never transmitted in the payload.
  - A CAPTCHA challenge is single-use and is consumed upon any login attempt, whether successful or failed.

### 3. Login
- **Endpoint**: `/login`
- **Method**: `POST`
- **Payload**:
```json
{
  "username": "exampleUser",
  "password": "secretPassword",
  "captcha": "ABCDE"
}
```
- **Responses**:
  - `200 OK`: `{"message": "Authenticated."}`
  - `422 Unprocessable Entity`: Validation errors (e.g., incorrect credentials or incorrect/expired CAPTCHA).
  - `419 Page Expired`: CSRF token mismatch or session expiration.
  - `429 Too Many Requests`: Rate limiting triggered.

### Rate Limiting
- Login attempts are throttled based on `username + IP address`.
- A maximum of 5 failed attempts per minute are allowed.
- Failed attempts due to incorrect CAPTCHAs or incorrect passwords both count towards the rate limit.

## User Migration Note

For legacy compatibility, the `username` column in the database is currently `nullable`. However, all application validation layers (FormRequests) and factory definitions enforce that `username` is strictly **required**. Existing users without a username will need to be migrated or assigned usernames before they can log in via this new flow.

## Error Handling

When authenticating via the frontend:
- General errors (invalid credentials, expired session) should be displayed via a generic alert.
- Validation errors (e.g. empty username, empty password, missing captcha) should be displayed inline.
- If the login fails for *any* reason, the frontend must immediately request a new CAPTCHA challenge and clear the captcha input field, as the previous challenge is consumed by the server.
