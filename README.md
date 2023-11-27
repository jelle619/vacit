# Vac!T
Vac!T is een simpele vacature website. Voor meer informatie, zie de documentatie in `/docs`.

## Development
Zorg dat Symfony geïnstalleerd is en gereed voor gebruik. Daarna kan je een kopie maken van de repository.

### Vereisten
De vereisten voor het runnen van dit project zijn als volgt:

- Een device die geschikt is voor Symfony, en waar dit al op geïnstalleerd is. Intructies zijn te vinden in de [Symfony-documentatie](https://symfony.com/download).
- Een MySQL database. Een vergelijkbare database werkt mogelijk ook, al zijn deze niet getest. Op Windows is het opzetten van MySQL het gemakkelijst door [XAMPP te installeren](https://www.apachefriends.org/download.html).
-  Een e-mail SMTP server. Deze is benodigd voor het versturen van bevestigingsmails wanneer een nieuw kandidaataccount geregistreerd word.

### Environmental Variables
Zorg dat je environment variables toevoegt aan een `.env` bestand in de root van het project voordat je de Symfony development server start. Neem het volgende als voorbeeld, en verander of vul aan daar waar nodig.

```
###> app/vacit ###
EMPLOYER_DEFAULT_PASSWORD=password
###< app/vacit ###

###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=a92085089q621c4759742f352b6fdef9
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
DATABASE_URL="mysql://root@127.0.0.1:3306/vacit?serverVersion=10.4.28-MariaDB&charset=utf8mb4"
###< doctrine/doctrine-bundle ###

###> symfony/messenger ###
# Choose one of the transports below
# MESSENGER_TRANSPORT_DSN=amqp://guest:guest@localhost:5672/%2f/messages
# MESSENGER_TRANSPORT_DSN=redis://localhost:6379/messages
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
###< symfony/messenger ###

###> symfony/mailer ###
MAILER_DSN=smtp://username:password@smtp.server.com:465
###< symfony/mailer ###
```

## Potentiële verbeteringen
Vac!T is bedoeld als een minimum viable product. Dit betekent dat sommige functies ontbreken, alhoewel o.a. de volgende veiligheidsmaatregelen geïmplementeerd zijn:
- E-mailverificatie
- Password hashing
- Password salting
- Login throttling
- CSRF mitigation

De volgende verbeteringen of veranderingen kunnen aangebracht worden om de gebruikerservaring te verbeteren:
- Een functie om een wachtwoord te resetten.
- Je profiel wijzigen zonder je wachtwoord dubbel in te vullen.
- Een manier om bedrijven toe te voegen zonder deze direct in de database aan te maken.

Verder kunnen de volgende wijzigingen aangebracht worden, afhankelijk van de wensen van de klant:
- CVs en profielfoto's kunnen privé gemaakt worden i.p.v. publiekelijk toegankelijk met de juiste URL.