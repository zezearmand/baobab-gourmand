# Le Baobab Gourmand — site WordPress (restaurant fictif)

## Installation rapide (Docker)
    docker compose up -d wordpress db
    # attendre ~30 s que WordPress initialise ses fichiers, puis :
    docker compose run --rm wpcli sh /setup/setup.sh
Le site est alors sur http://localhost:8080 (les identifiants sont affichés en fin de script).

## Installation sur un WordPress existant
1. Copier `wp-content/themes/baobab-gourmand` dans `wp-content/themes/`.
2. Copier `setup.sh` et `content/` dans un même dossier, puis, depuis la racine WordPress : `SITE_URL=https://mon-site.tld sh /chemin/setup.sh` (WP-CLI requis).

## Gestion par le responsable
Compte « gerant » (rôle Éditeur) : Pages, Articles, **Carte & offres** (plats, prix, rubriques), Médias, Menus.
