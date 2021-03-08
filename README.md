# GazeHub

## Generate Keypair

```bash
# generate private key
openssl genrsa -out private.key 4096

# generate public key
openssl rsa -in private.key -outform PEM -pubout -out public.key
```