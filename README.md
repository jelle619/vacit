# Vac!T
Vac!T is een simpele vacature website. Voor meer informatie, zie de documentatie in `/docs`.

## Development
Zorg dat Symfony geïnstalleerd is en gereed voor gebruik. Daarna kan je een kopie maken van de repository. Zorg dat je environment variables toevoegt aan een `.env` bestand in de root van het project voordat je de Symfony development server start. Neem het volgende als voorbeeld.

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
