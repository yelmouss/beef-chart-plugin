# Options d'icônes pour le menu admin Beef Chart

## Icône actuelle : SVG personnalisé de bœuf
Nous avons implémenté une icône SVG personnalisée qui représente des coupes de bœuf avec des formes organiques.

## Alternatives Dashicons disponibles :

### Option 1: `dashicons-drumstick` (Recommandé)
- Représente la viande/volaille
- Icône native WordPress
- Simple et reconnaissable

### Option 2: `dashicons-food` 
- Icône générale de nourriture
- Peut représenter l'industrie alimentaire

### Option 3: `dashicons-carrot`
- Représente l'alimentation/nutrition
- Plus végétal mais reste dans le domaine alimentaire

### Option 4: `dashicons-store`
- Représente un commerce/boutique
- Approprié pour les boucheries

## Comment changer l'icône :

Pour revenir à une icône Dashicon simple, remplacez dans la fonction `add_admin_menu()` :

```php
// Au lieu de
$beef_icon = $this->get_beef_icon_svg();

// Utilisez directement
'dashicons-drumstick',  // ou 'dashicons-food', 'dashicons-store', etc.
```

## Icône actuelle
L'icône SVG personnalisée a été optimisée pour :
- ✅ Compatibilité avec le thème admin WordPress
- ✅ Adaptation automatique aux couleurs du thème
- ✅ Résolution parfaite à toutes les tailles
- ✅ Représentation symbolique des coupes de bœuf