# Car Catalog API

API REST per la gestione di un catalogo di automobili sviluppata con Symfony 7.2.

## Requisiti di Sistema

- PHP 8.1 o superiore
- Composer
- MySQL 8.0 o superiore
- Symfony CLI (opzionale)

## Installazione

1. Clonare il repository
```bash
git clone https://github.com/Nik0-9/car_catalog_symfony.git
cd car-catalog
```

2. Installare le dipendenze
```bash
composer install
```

3. Configurare il database
- Copiare il file `.env` in `.env.local`
- Modificare la variabile DATABASE_URL nel file `.env.local` con i propri parametri di connessione:
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/car_catalog?serverVersion=8.0.32&charset=utf8mb4"
```

4. Creare il database e applicare le migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Avvio dell'Applicazione

Con Symfony CLI:
```bash
symfony server:start
```

## Endpoints API

### GET /api/cars
Recupera la lista di tutte le auto non cancellate

### GET /api/car/{id}
Recupera i dettagli di una specifica auto

### POST /api/car
Crea una nuova auto
```json
{
    "brand": "Toyota",
    "model": "Corolla",
    "price": 20000,
    "production_year": 2020
}
```

### PUT /api/car/{id}
Aggiorna completamente un'auto esistente
```json
{
    "brand": "Toyota",
    "model": "Corolla",
    "price": 25000,
    "production_year": 2020
}
```

### PATCH /api/car/{id}
Aggiorna parzialmente un'auto esistente
```json
{
    "price": 25000,
    "status": "sold"
}
```

### DELETE /api/car/{id}
Effettua una cancellazione soft di un'auto

## Esecuzione dei Test

Per eseguire i test unitari:
```bash
php bin/phpunit
```

## Gestione degli Errori

L'API gestisce i seguenti codici di errore:
- 200: Successo
- 400: Richiesta non valida
- 404: Risorsa non trovata
- 405: Metodo non consentito
- 500: Errore interno del server

## Note Aggiuntive

- L'API implementa il soft delete per le automobili
- Lo stato di un'auto può essere "available" o "sold"
- Tutti i prezzi sono gestiti con precisione di 2 decimali