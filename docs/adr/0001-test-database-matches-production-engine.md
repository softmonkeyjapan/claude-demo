# 1. L'environnement de test utilise le même moteur de base de données que la production

## Statut

Acceptée

## Contexte

Les tests (`phpunit.xml`) tournaient sur SQLite en mémoire, tandis que la configuration de production (`.env.example`) cible Postgres. Le ticket #4 (Repository + Service Contact) demandait une recherche `LIKE` insensible à la casse. SQLite est insensible à la casse par défaut sur les caractères ASCII, mais Postgres est sensible à la casse par défaut pour `LIKE` — un test vert sur SQLite ne garantissait donc rien sur le comportement réel en production.

La première version du correctif enveloppait chaque comparaison dans `LOWER(...)` côté code applicatif, pour obtenir un comportement identique quel que soit le moteur. Cette solution fonctionnait, mais ajoutait de la complexité dans le code (`ContactRepository`) pour compenser un écart d'environnement — l'écart lui-même restait non traité.

## Décision

L'environnement de test local utilise désormais Postgres (via Docker), le même moteur qu'en production — plus de SQLite. `phpunit.xml` pointe vers une base `claude_demo_test` dédiée, servie par le conteneur défini dans `docker-compose.yml`.

Conséquence directe : le code applicatif n'a plus besoin de logique de portabilité inter-moteurs. `ContactRepository::search()` utilise directement l'opérateur natif Postgres `ILIKE`, sans `LOWER()` explicite.

## Conséquences

- **Positif** : un test vert garantit désormais un comportement identique en production — plus d'écart silencieux entre l'environnement de test et la réalité.
- **Positif** : le code métier reste simple, sans logique de portabilité pour des moteurs qu'on n'utilise jamais réellement.
- **Négatif** : Docker est désormais un prérequis pour lancer la suite de tests en local (voir le README pour l'installation). `php artisan test` ne fonctionne plus "out of the box" sans setup préalable.
- **Négatif** : le code (`ILIKE`) est désormais explicitement couplé à Postgres — un changement de moteur de base de données en production nécessiterait de revoir cette méthode.
