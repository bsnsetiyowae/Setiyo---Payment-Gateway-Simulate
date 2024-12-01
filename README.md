


# Technical Testing - Setiyo

Api ini berisi fitur Deposit, Withdrawal, Balance, dan History Transaction
laravel version 10

## Setup Engine 
 - Clone engine dari github : https://github.com/bsnsetiyowae/Setiyo---Payment-Gateway-Simulate
 - Masuk ke dalam project dari terminal / prompt
 - Checkout ke branch : main
 - Jalankan composer install
 - Sesuaikan connection database pada file .env
 - Pada terminal jalankan perintah : ***php artisan migrate*** dan ***php artisan db:seed***
 - jalankan php artisan serve (mendapatkan link base url)

## Fiture
Untuk mengakses endpoin fitur, gunakan bearer token : **U2V0aXlv** 
(base64_encode dari 'Setiyo')
### Deposit

 - POST : {BASE_URL}/api/deposit
 - Payload : order_id, amount
### Withdraw
 - POST : {BASE_URL}/api/withdraw
 - Payload : amount
### Balance
GET: {BASE_URL}/api/ewallet/balance
### Transaction History
POST : {BASE_URL}/api/transactions/history
