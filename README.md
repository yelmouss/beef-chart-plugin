# Beef Chart for WordPress

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![GitHub issues](https://img.shields.io/github/issues/yourusername/beef-chart-plugin)](https://github.com/yourusername/beef-chart-plugin/issues)
[![GitHub stars](https://img.shields.io/github/stars/yourusername/beef-chart-plugin)](https://github.com/yourusername/beef-chart-plugin/stargazers)

An interactive beef cuts visualization plugin for WordPress that allows butchers and meat shops to display their beef cuts with real-time pricing on an interactive SVG map.

## 🌟 Features

- **Interactive SVG Map**: Beautiful, clickable beef cuts map using ECharts
- **Real-time Pricing**: Dynamic price updates without page refresh
- **Admin Management**: Easy-to-use admin interface for managing prices and availability
- **Shortcode Support**: Simple integration with `[beef_chart]` shortcode
- **Gutenberg Block**: Native WordPress block editor support
- **Responsive Design**: Works perfectly on all devices
- **Multi-language Ready**: Internationalization support
- **Security First**: Prepared statements and nonce verification
- **Role-based Access**: Accessible to editors and administrators

## 📸 Screenshots

### Admin Interface
*Admin interface showing price management and preview*

### Frontend Display
*Interactive beef cuts map on frontend*

## 🚀 Installation

### Method 1: WordPress Admin (Recommended)
1. Go to **Plugins > Add New** in your WordPress admin
2. Search for "Beef Chart Plugin"
3. Click **Install Now** and then **Activate**

### Method 2: Manual Installation
1. Download the plugin ZIP file from [GitHub Releases](https://github.com/yelmouss/beef-chart-plugin/releases)
2. Go to **Plugins > Add New > Upload Plugin**
3. Upload the ZIP file and click **Install Now**
4. Activate the plugin

### Method 3: Git Clone
```bash
cd wp-content/plugins/
git clone https://github.com/yourusername/beef-chart-plugin.git
cd beef-chart-plugin
# Activate via WordPress admin or WP-CLI
wp plugin activate beef-chart-plugin
```

## ⚙️ Configuration

### Basic Setup
1. After activation, go to **Beef Chart** in your admin menu
2. Configure prices and availability for each beef cut
3. Use the preview to see how it will look on your site

### Shortcode Usage
```php
[beef_chart]
[beef_chart width="800px" height="500px"]
[beef_chart width="100%" height="600px"]
```

### Gutenberg Block
1. Add a new block in the Gutenberg editor
2. Search for "Beef Chart"
3. Configure width and height as needed

## 🔧 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **JavaScript**: ES6+ support (modern browsers)

## 📋 Beef Cuts Included

The plugin includes 29 traditional French beef cuts:
- Filet (Filet)
- Entrecôte (Ribeye)
- Rumsteck (Rump steak)
- Faux-filet (Sirloin)
- Côtes (Ribs)
- And many more...

## 🛡️ Security Features

- **Prepared Statements**: All database queries use prepared statements
- **Nonce Verification**: CSRF protection on all forms
- **Capability Checks**: Proper WordPress capability verification
- **Input Sanitization**: All user inputs are sanitized
- **SQL Injection Protection**: Parameterized queries prevent SQL injection

## 🌐 Internationalization

The plugin is ready for translation. To contribute translations:

1. Fork the repository
2. Add your language files to `/languages/`
3. Submit a pull request

Current language support:
- English (en_US)
- French (fr_FR)

## 🛠️ Development

### Prerequisites
- Node.js 16+
- npm or yarn
- Local WordPress development environment

### Setup for Development
```bash
# Clone the repository
git clone https://github.com/yourusername/beef-chart-plugin.git
cd beef-chart-plugin

# Install dependencies (if any)
npm install

# Build assets (if applicable)
npm run build
```

### File Structure
```
beef-chart-plugin/
├── beef-chart-plugin.php     # Main plugin file
├── beef-chart-final-complete.js # Frontend JavaScript
├── admin.js                  # Admin JavaScript
├── block.js                  # Gutenberg block
├── Beef_cuts_France.svg      # Beef cuts map
├── assets/                   # Static assets
├── languages/                # Translation files
└── README.md                 # This file
```

### Contributing
1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin feature/your-feature`
5. Submit a pull request

### Coding Standards
- Follow WordPress Coding Standards
- Use meaningful variable names
- Add comments for complex logic
- Test on multiple WordPress versions

## 📊 Database Structure

The plugin creates one table: `wp_beef_chart_data`

```sql
CREATE TABLE wp_beef_chart_data (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    price decimal(10,2) NOT NULL,
    available boolean DEFAULT true,
    PRIMARY KEY (id)
);
```

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Interactive beef cuts map
- Admin management interface
- Shortcode and Gutenberg support
- Security hardening
- Internationalization support

## 📝 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 Yelmouss

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## 🙏 Credits

- **ECharts**: For the amazing charting library
- **Apache ECharts**: For providing the beef cuts SVG
- **WordPress Community**: For the robust platform
- **Yelmouss**: For development and maintenance

## 📞 Support

### Getting Help
- **Documentation**: [GitHub Wiki](https://github.com/yelmouss/beef-chart-plugin/wiki)
- **Issues**: [GitHub Issues](https://github.com/yelmouss/beef-chart-plugin/issues)
- **Discussions**: [GitHub Discussions](https://github.com/yelmouss/beef-chart-plugin/discussions)

### Professional Support
For custom development, premium support, or consulting:
- Email: yelmouss.devt@gmail.com
- Website: [Yelmouss](https://yelmouss.vercel.app)
- Phone: +212 612 865681

## 🎯 Roadmap

- [ ] Mobile app companion
- [ ] Advanced analytics dashboard
- [ ] Multi-currency support
- [ ] Custom beef cut definitions
- [ ] Integration with e-commerce platforms
- [ ] REST API endpoints
- [ ] Bulk import/export functionality

## 📈 Performance

- **Lightweight**: Minimal impact on site performance
- **Optimized**: Efficient database queries
- **Cached**: Browser caching for static assets
- **CDN Ready**: Assets can be served from CDN

---

**Made with ❤️ by [Yelmouss](https://yelmouss.vercel.app)**

*Empowering butchers with modern web technology*

**Options du shortcode :**
```
[beef_chart width="100%" height="600px"]
```

- `width` : Largeur du graphique (par défaut : 100%)
- `height` : Hauteur du graphique (par défaut : 600px)

#### Méthode 2 : Bloc Gutenberg

1. Créez ou modifiez une page avec l'éditeur Gutenberg
2. Cliquez sur le bouton **+** pour ajouter un bloc
3. Recherchez "Beef Chart" ou allez dans la catégorie **Widgets**
4. Sélectionnez le bloc **Beef Chart**
5. Le bloc s'affiche avec un aperçu (le graphique réel apparaîtra sur la page publiée)

#### Méthode 3 : Éditeur Classique

1. Dans l'éditeur classique, passez en mode **Texte** (onglet en haut à droite)
2. Ajoutez le shortcode à l'endroit souhaité :

```
[beef_chart]
```

### 3. Aperçu et test

- **Aperçu dans l'éditeur** : Le graphique ne s'affiche pas en mode aperçu, seulement sur la page publiée
- **Test** : Publiez la page et visitez-la pour voir le graphique interactif
- **Responsive** : Le graphique s'adapte automatiquement aux écrans mobiles

## Personnalisation avancée

### Modifier les données par défaut

Les données par défaut sont définies dans le fichier `beef-chart-plugin.php` dans la fonction `insert_default_data()`. Vous pouvez les modifier selon vos besoins.

### Styles personnalisés

Vous pouvez ajouter des styles CSS personnalisés dans votre thème pour modifier l'apparence du graphique.

## Dépannage

### Le graphique ne s'affiche pas

1. Vérifiez que le plugin est activé
2. Assurez-vous que JavaScript est activé dans votre navigateur
3. Vérifiez la console du navigateur pour les erreurs

### Les modifications ne sont pas sauvegardées

1. Vérifiez que vous avez les droits d'administrateur
2. Assurez-vous que la table de base de données existe
3. Vérifiez les permissions d'écriture sur la base de données

### Problèmes de performance

Si le graphique est lent à charger, vous pouvez :
- Réduire la hauteur du graphique
- Optimiser les images du thème
- Utiliser un cache WordPress

## Support

Pour toute question ou problème, contactez l'équipe de développement de Yelmouss.

## Structure des fichiers

```
beef-chart-plugin/
├── beef-chart-plugin.php    # Fichier principal du plugin
├── js/
│   ├── beef-chart-component.js    # Composant React
│   ├── admin.js                   # Scripts admin
│   └── block.js                   # Bloc Gutenberg
├── assets/
│   └── Beef_cuts_France.svg       # Carte SVG des coupes
├── build/                         # Fichiers build Next.js
└── README.md                      # Ce fichier
```
├── beef-chart-plugin.php    # Fichier principal du plugin
├── js/
│   ├── admin.js            # JavaScript pour l'admin
│   └── beef-chart-component.js  # Composant React (non utilisé dans la version actuelle)
├── assets/                 # Assets statiques (SVG, images)
├── build/                  # Fichiers buildés de Next.js
└── README.md              # Ce fichier
```

## Technologies utilisées

- **WordPress** : CMS
- **React** : Interface utilisateur
- **Material-UI** : Composants UI
- **ECharts** : Graphiques interactifs
- **MySQL** : Base de données

## Développement

Ce plugin a été développé à partir d'une application Next.js existante. Pour modifier le composant React :

1. Modifiez les fichiers dans le dossier `src/` du projet Next.js
2. Build le projet : `npm run build`
3. Copiez les fichiers buildés dans le dossier `build/` du plugin

## Support

Pour toute question ou support, contactez l'équipe de développement de Yelmouss.

## Licence

Ce plugin est sous licence GPL v2 ou supérieure.
