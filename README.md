# French Beef Cuts Chart – Guide d’installation rapide

Plugin WordPress pour afficher une carte interactive des coupes de bœuf françaises avec gestion des prix et de la disponibilité.

## Prérequis

- WordPress 5.0 ou supérieur
- PHP 7.4 ou supérieur

## Installation

Méthode 1 — depuis l’admin WordPress (recommandée)
1. Allez dans Extensions > Ajouter > Téléverser une extension
2. Sélectionnez l’archive ZIP du plugin (beef-chart-final.zip)
3. Cliquez sur Installer, puis Activer

Méthode 2 — installation manuelle (FTP/SFTP)
1. Décompressez le ZIP du plugin
2. Copiez le dossier `beef-chart-final/` dans `wp-content/plugins/`
3. Allez dans Extensions et cliquez sur Activer

Après activation
- Un menu « Beef Chart » apparaît dans l’admin (barre latérale)
- La table de données est créée automatiquement et remplie avec 29 coupes par défaut

## Utilisation

Option A — Shortcode (pages / articles)
- Basique: `[beef_chart]`
- Avec dimensions: `[beef_chart width="100%" height="600px"]`

Option B — Bloc Gutenberg
1. Dans l’éditeur, cliquez sur « + » pour ajouter un bloc
2. Recherchez « Beef Chart »
3. Insérez le bloc et ajustez les dimensions si besoin

## Configuration (admin)

- Menu « Beef Chart » > modifiez les prix (€/kg) et la disponibilité
- Aperçu en haut de page pour visualiser le rendu du graphique
- Bouton « Réinitialiser les données » disponible en cas de problème (recrée les 29 coupes par défaut)

## Conseils et dépannage

- Le graphique ne s’affiche pas en aperçu de l’éditeur ? Affichez la page publiée
- Si rien ne s’affiche en front : vérifiez que le shortcode est présent et que l’extension est activée
- Pour des dimensions personnalisées, utilisez les attributs `width` et `height` du shortcode

## Licence & Auteur

- Licence: GPL v2 
- Auteur: Yelmouss (https://yelmouss.vercel.app)
