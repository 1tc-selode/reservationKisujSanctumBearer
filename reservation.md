# Reservation API – Authentikált végpontok (Sanctum + Bearer token)

Alap URL (base URL):

http://localhost/reservationKisujSanctumBearer/api

Az összes lentebbi végpont ehhez képest értendő, tehát pl. a login teljes URL-je:

http://localhost/reservationKisujSanctumBearer/api/login

A Laravel-ben az útvonalak (részlet):

```php
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

// Authentikált végpontok
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);
Route::middleware('auth:sanctum')->get('/reservations',[ReservationController::class, 'index']); 
Route::middleware('auth:sanctum')->get('/reservations/{id}',[ReservationController::class, 'show']); 
Route::middleware('auth:sanctum')->post('/reservations',[ReservationController::class, 'store']); 
Route::middleware('auth:sanctum')->put('/reservations/{id}',[ReservationController::class, 'update']); 
Route::middleware('auth:sanctum')->patch('/reservations/{id}',[ReservationController::class, 'update']); 
Route::middleware('auth:sanctum')->delete('/reservations/{id}',[ReservationController::class, 'destroy']); 
```

## 1. Regisztráció – `POST /register` (nyilvános)

Új felhasználó létrehozása (sima user, nem admin).

Teljes URL:

`POST http://localhost/reservationKisujSanctumBearer/api/register`

Headers (Postman):

- `Content-Type: application/json`
- `Accept: application/json`

Body (raw JSON):

```json
{
  "name": "Teszt User",
  "email": "tesztuser@example.com",
  "password": "Jelszo_2025"
}
```

Válasz – siker esetén (példa):

- HTTP státuszkód: `201 Created`

```json
{
  "message": "User regisztrációja kész!",
  "user": {
    "id": 3,
    "name": "Teszt User",
    "email": "tesztuser@example.com",
    "is_admin": false
  }
}
```

## 2. Bejelentkezés – `POST /login` (nyilvános)

Belépés email + jelszóval, és egy Bearer token visszaadása.  
Ezt a tokent kell majd az összes védett végpontnál beküldeni.

Teljes URL:

`POST http://localhost/reservationKisujSanctumBearer/api/login`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`

Body (raw JSON):

```json
{
  "email": "tesztuser@example.com",
  "password": "Jelszo_2025"
}
```

Válasz – siker esetén (példa):

- HTTP státuszkód: `200 OK`

```json
{
  "access_token": "1|valami_hosszu_sanctum_token...",
  "token_type": "Bearer"
}
```

A kapott access_token-t a további kérésekben így kell használni:

Authorization header:

`Authorization: Bearer 1|valami_hosszu_sanctum_token...`

## 3. Kijelentkezés – `POST /logout` (auth:sanctum)

Minden ehhez a felhasználóhoz tartozó token törlése (kijelentkezés).

Laravel route:

```php
Route::middleware('auth:sanctum')->post('/logout',[AuthController::class,'logout']);
```

Teljes URL:

`POST http://localhost/reservationKisujSanctumBearer/api/logout`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <A_LOGINNÉL_KAPOTT_TOKEN>`

Példa:

`Authorization: Bearer 1|valami_hosszu_sanctum_token...`

Body:

- Nem szükséges (üresen maradhat).

Válasz – siker esetén:

- HTTP státuszkód: `200 OK`

```json
{
  "message": "Logged out successfully"
}
```

Utána ezzel a tokennel már nem lehet hívni a védett végpontokat → `401 Unauthorized`.

## 4. Összes foglalás lekérdezése – `GET /reservations` (auth:sanctum)

- Admin: látja az összes foglalást.  
- Normál user: csak a saját foglalásait látja.

Laravel route:

```php
Route::middleware('auth:sanctum')->get('/reservations',[ReservationController::class, 'index']);
```

Teljes URL:

`GET http://localhost/reservationKisujSanctumBearer/api/reservations`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body:

- Nincs (GET kérés).

Válasz – siker esetén (példa adminnál):

```json
[
  {
    "id": 1,
    "user_id": 1,
    "reservation_time": "2025-11-10 18:00:00",
    "guests": 4,
    "note": "Születésnapi vacsora"
  },
  {
    "id": 2,
    "user_id": 2,
    "reservation_time": "2025-12-01 19:00:00",
    "guests": 3,
    "note": "Baráti vacsora"
  }
]
```

Sima usernél csak a saját foglalásai jelennek meg.

## 5. Egy konkrét foglalás lekérdezése – `GET /reservations/{id}` (auth:sanctum)

- Admin: tetszőleges foglalást lekérdezhet.  
- Normál user: csak a saját foglalását érheti el → ha nem az övé, `403 Unauthorized`.

Laravel route:

```php
Route::middleware('auth:sanctum')->get('/reservations/{id}',[ReservationController::class, 'show']);
```

Teljes URL példa:

`GET http://localhost/reservationKisujSanctumBearer/api/reservations/1`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body:

- Nincs.

Válasz – siker esetén (példa):

```json
{
  "id": 1,
  "user_id": 1,
  "reservation_time": "2025-11-10 18:00:00",
  "guests": 4,
  "note": "Születésnapi vacsora"
}
```

Ha nem a saját foglalás, és a user nem admin:

- HTTP státuszkód: `403 Forbidden`

```json
{
  "message": "Unauthorized"
}
```

## 6. Új foglalás létrehozása – `POST /reservations` (auth:sanctum)

Új foglalás rögzítése a bejelentkezett user nevében.  
user_id-t nem a kliens küldi, hanem a backend tölti ki a bejelentkezett user alapján.

Laravel route:

```php
Route::middleware('auth:sanctum')->post('/reservations',[ReservationController::class, 'store']);
```

Teljes URL:

`POST http://localhost/reservationKisujSanctumBearer/api/reservations`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body (raw JSON):

```json
{
  "reservation_time": "2025-12-01 19:00:00",
  "guests": 3,
  "note": "Baráti vacsora"
}
```

A backend így menti:

```php
$reservation = Reservation::create([
    'user_id' => $request->user()->id,
    'reservation_time' => $validated['reservation_time'],
    'guests' => $validated['guests'],
    'note' => $validated['note'] ?? null,
]);
```

Válasz – siker esetén:

- HTTP státuszkód: `201 Created`

```json
{
  "id": 5,
  "user_id": 3,
  "reservation_time": "2025-12-01 19:00:00",
  "guests": 3,
  "note": "Baráti vacsora",
  "created_at": "...",
  "updated_at": "..."
}
```

## 7. Foglalás teljes módosítása – `PUT /reservations/{id}` (auth:sanctum)

Foglalás összes mezőjének módosítása (kivéve user_id).  
Csak az módosíthatja, akinek a foglalása, vagy az admin.

Laravel route:

```php
Route::middleware('auth:sanctum')->put('/reservations/{id}',[ReservationController::class, 'update']);
```

Teljes URL példa:

`PUT http://localhost/reservationKisujSanctumBearer/api/reservations/5`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body (raw JSON – minden mező):

```json
{
  "reservation_time": "2025-12-05 20:00:00",
  "guests": 5,
  "note": "Módosítva: céges vacsora"
}
```

Válasz – siker esetén:

- HTTP státuszkód: `200 OK`

```json
{
  "id": 5,
  "user_id": 3,
  "reservation_time": "2025-12-05 20:00:00",
  "guests": 5,
  "note": "Módosítva: céges vacsora",
  "created_at": "...",
  "updated_at": "..."
}
```

Ha más foglalását akarja módosítani nem adminként:

- HTTP státuszkód: `403 Forbidden`

```json
{
  "message": "Unauthorized"
}
```

## 8. Foglalás részleges módosítása – `PATCH /reservations/{id}` (auth:sanctum)

Csak néhány mező módosítása (pl. csak guests, vagy csak note).

Laravel route:

```php
Route::middleware('auth:sanctum')->patch('/reservations/{id}',[ReservationController::class, 'update']);
```

Teljes URL példa:

`PATCH http://localhost/reservationKisujSanctumBearer/api/reservations/5`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body (raw JSON – csak a módosítandó mezők):

Példa 1 – csak vendégek száma:

```json
{
  "guests": 6
}
```

Példa 2 – csak megjegyzés:

```json
{
  "note": "Csak a megjegyzés módosítva"
}
```

A validáció `sometimes|required`, ezért csak azokat kéri számon, amiket elküldünk.

Válasz – siker esetén:

- HTTP státuszkód: `200 OK`
- A frissített foglalás JSON-ben.

## 9. Foglalás törlése – `DELETE /reservations/{id}` (auth:sanctum)

Egy foglalás teljes törlése az adatbázisból.

Laravel route:

```php
Route::middleware('auth:sanctum')->delete('/reservations/{id}',[ReservationController::class, 'destroy']);
```

Teljes URL példa:

`DELETE http://localhost/reservationKisujSanctumBearer/api/reservations/5`

Headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <TOKEN>`

Body:

- Nem kell body.

Válasz – siker esetén:

- HTTP státuszkód: `200 OK`

```json
{
  "message": "Foglalás törölve."
}
```

Ha nem a saját foglalás és a user nem admin:

- HTTP státuszkód: `403 Forbidden`

```json
{
  "message": "Unauthorized"
}
```
