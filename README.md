# Car Catalog API

API REST per la gestione di un catalogo di automobili sviluppata con Symfony 7.2.

## Requisiti di Sistema

- PHP 8.1 o superiore
- Composer
- MySQL 8.0 o superiore
- Symfony CLI

## Guida all'Installazione di Symfony CLI

Prima di installare Symfony CLI, assicurati di avere:
- Git

guida all'installazione: <https://symfony.com/download>

## Installazione Progetto

1. Clonare il repository

```bash
git clone https://github.com/Nik0-9/car_catalog_symfony.git
cd car_catalog_symfony
```

2. Installare le dipendenze

```bash
composer install
```

3. Configurare il database

- Creare un nuovo file .env.local e copiare il contenuto di .env, poi modificare la variabile DATABASE_URL con i propri parametri di connessione

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/car_catalog?serverVersion=8.0.32&charset=utf8mb4"
```

4. Creare il database e applicare le migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Carica i dati di test:

```bash
php bin/console doctrine:fixtures:load
```

## Avvio dell'Applicazione

Con Symfony CLI:

```bash
symfony server:start
```

L'applicazione sarà disponibile all'indirizzo: `http://127.0.0.1:8000`

## Endpoints API

### GET /api/cars

Recupera la lista di tutte le auto non cancellate

### GET /api/car/{id}

Recupera i dettagli di una specifica auto

### POST /api/car

Crea una nuova auto

```json
{
    "brand": "Brand X",
    "model": "Model Y",
    "price": 20000,
    "production_year": 2020
}
```

### PUT /api/car/{id}

Aggiorna completamente un'auto esistente

```json
{
    "brand": "Brand X",
    "model": "Model Y",
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

### SEARCH /api/cars/search

- Effettua ricerca filtrata, filtri disponibili:

#### modello /api/cars/search?brand=toyota

- recupera la lista di tutte le Toyota

#### disponibili/vendute /api/cars/search?status=available \ search?status=sold

- recupera la lista di tutte le auto disponibili o vendute

#### prezzo minimo /api/cars/search?min_price=10000

- esclude dalla lista tutte le auto con prezzo inferioire a 10000

#### prezzo massimo /api/cars/search?max_price=10000

- esclude dalla lista tutte le auto con prezzo superiore a 10000

#### fascia di prezzo /api/cars/search?min_price=10000&max_price=20000

- recupera la lista delle auto con prezzo compreso tra 10000 e 20000

#### I filtri si posso usare singolarmente e concatenati

## Esecuzione dei Test

Per eseguire i test unitari:

```bash
php bin/phpunit
```

## Paginazione

Endpoint come GET /api/cars e SEARCH /api/cars/search restituiscono un limite di 10 elementi per pagina, è possibile aggiungere parametri come:

page=n per visualizzare una pagina specifica

limit=n per ridurre o aumentare il numero di risultati per pagina

## Gestione degli Errori

L'API gestisce i seguenti codici di errore:

- 200: Successo
- 400: Richiesta non valida
- 404: Risorsa non trovata
- 405: Metodo non consentito
- 422: Errore durante la validazione
- 500: Errore interno del server
- 503: Errore connessione con il database