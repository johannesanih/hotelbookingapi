#!/bin/bash

URL="https://automatic-lamp-45jgrvp795vhxv-8000.app.github.dev/api/v1/customer/register"

echo "Registering a new customer..."

curl -X POST $URL \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
--data-binary @- <<EOF | jq
{
    "firstname": "John",
    "lastname": "Doe",
    "email": "john.doe@example.com",
    "phonenumber": "1234567890",
    "password": "00000000",
    "password_confirmation": "00000000"
}
EOF

echo -e "\nCustomer registration completed."
