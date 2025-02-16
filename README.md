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

## Windows

### Installazione tramite Scoop (Raccomandato)
1. Installa Scoop (se non lo hai già):
```powershell
irm get.scoop.sh -outfile 'install.ps1'
.\install.ps1 -RunAsAdmin
```

2. Installa Symfony CLI:
```powershell
scoop install symfony-cli
```

### Installazione tramite Installer Windows
1. Vai su https://symfony.com/download
2. Scarica il file .exe per Windows
3. Esegui il file scaricato
4. Segui le istruzioni dell'installazione guidata

## macOS

### Installazione tramite Homebrew
1. Installa Homebrew (se non lo hai già):
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```

2. Installa Symfony CLI:
```bash
brew install symfony-cli/tap/symfony-cli
```

## Linux

### Debian/Ubuntu
```bash
# Aggiungi il repository
echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | sudo tee /etc/apt/sources.list.d/symfony-cli.list

# Aggiorna e installa
sudo apt update
sudo apt install symfony-cli
```

### Altre distribuzioni Linux (usando curl)
```bash
# Per Linux x86_64
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
sudo apt install symfony-cli
```

## Verifica dell'Installazione

Dopo l'installazione, verifica che tutto funzioni correttamente eseguendo:
```bash
symfony -V
```

Dovresti vedere la versione di Symfony CLI installata.

## Problemi Comuni

### Windows
- Se ricevi errori di permessi, assicurati di eseguire PowerShell come amministratore
- Verifica che il PATH di sistema includa la directory di installazione di Symfony CLI

### Linux
- Se ricevi errori durante l'aggiunta del repository, verifica di avere i permessi sudo
- Assicurati che `curl` sia installato (`sudo apt install curl` su Debian/Ubuntu)

### macOS
- Se Homebrew non è aggiornato, esegui `brew update` prima dell'installazione
- In caso di problemi con i permessi, verifica i permessi della directory con `ls -la /usr/local/bin`

## Note Aggiuntive

- È consigliabile mantenere Symfony CLI aggiornato. Usa il comando appropriato per il tuo sistema:
  - Windows (Scoop): `scoop update symfony-cli`
  - macOS: `brew upgrade symfony-cli`
  - Linux: `sudo apt update && sudo apt upgrade symfony-cli`

- Per disinstallare Symfony CLI:
  - Windows (Scoop): `scoop uninstall symfony-cli`
  - macOS: `brew uninstall symfony-cli`
  - Linux: `sudo apt remove symfony-cli`

## Risorse Utili
- [Documentazione ufficiale Symfony](https://symfony.com/doc/current/setup.html)
- [Repository GitHub Symfony CLI](https://github.com/symfony/cli)
- [Forum della community Symfony](https://symfony.com/community)

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
- 503: Errore connessione con il database