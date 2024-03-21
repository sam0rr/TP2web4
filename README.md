# Projet gabarit ZEPHYRUS

Projet destiné à être utilisé comme base de gabarit pour les projets ZEPHYRUS.

## Environnement de développement (Docker)

### Prérequis
Assurez-vous d'avoir Composer installer sur le poste de développement depuis Brew sur MacOs. Si Composer n'est pas
installé, procéder à son installation avec la commande `brew install composer`. 

Ensuite, assurez-vous d'avoir le [Moteur Docker](https://www.docker.com/products/docker-desktop/) installé et à jour.

Finalement, vous aurez besoin de [Mutagen Compose](https://mutagen.io/documentation/orchestration/compose) qui s'assure que les fichiers sont correctement synchronisés pour optimiser la compilation de Pug. Si
Mutagen Compose n'est pas installé, procéder à son installation avec la commande `brew install mutagen-io/mutagen/mutagen-compose`.

### Premier démarrage
```sh
composer install --ignore-platform-reqs
mutagen-compose up
```

### Mise à jour des dépendances (Composer)
```sh
composer update --ignore-platform-reqs
```

### Redémarrer la base de données (au besoin)
```sh
mutagen-compose down
mutagen-compose up
```
