#!bin/bash

URL="https://automatic-lamp-45jgrvp795vhxv-8000.app.github.dev/api/v1/hotel-admin/register"

echo "Registering a new hotel admin..."

curl -X POST $URL \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
--data-binary @- <<EOF
{
    "firstname": "Alice",
    "lastname": "Smith",
    "email": "alice.smith@example.com",
    "phonenumber": "0987654321",
    "password": "00000000",
    "password_confirmation": "00000000"
}
EOF

echo -e "\nHotel admin registration completed."
