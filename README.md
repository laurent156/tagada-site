# Tagada — Studio créatif

Page d'accueil pour Tagada, un studio de fabrication de décors et enseignes sur-mesure basé à Bruxelles.

## Contexte

Traduction d'une maquette Figma (Relume Kit) dans le design system extrait du template Webflow [orange-template.webflow.io](https://orange-template.webflow.io/) : palette, typographie (Archivo / Inter Tight), rayons, easing, et composants (accordéon FAQ à "rideau", menu overlay avec ancres, panneau vidéo type showreel, hover sur les cartes projet).

## Pages

- `index.html` — page d'accueil.
- `mentions-legales.html` — identification légale de Tagada SRL (obligatoire en Belgique pour tout site professionnel, indépendamment de la vente en ligne).
- `confidentialite.html` — politique de confidentialité RGPD (formulaire de contact + Google Analytics).
- `cookies.html` — détail des cookies utilisés et gestion des préférences.

Chaque page est un fichier autonome — polices et images encodées en base64, aucune dépendance externe. Peuvent être ouvertes directement dans un navigateur ou déployées telles quelles sur n'importe quel hébergeur statique (Netlify, Vercel, GitHub Pages, etc.).

**Pas de "Conditions générales"** : non nécessaires, le site ne vend rien en ligne (pas de panier, pas de contrat conclu à distance). Les CGV sont remplacées par les Mentions légales, qui elles sont obligatoires quel que soit le modèle du site.

## Cookies & Google Analytics

Un bandeau de consentement (accepter/refuser) est présent sur toutes les pages, avec le choix mémorisé dans `localStorage` (`tagada_consent`). Tant que Google Analytics n'a pas d'ID, le bandeau s'affiche et fonctionne mais ne charge aucun script — c'est une coquille prête à l'emploi.

**Avant de brancher Google Analytics** : dans chaque page HTML, chercher la ligne
```js
var GA_MEASUREMENT_ID = ''; // TODO : coller l'ID Google Analytics (format G-XXXXXXXXXX) une fois le compte créé
```
et y coller l'ID de mesure (`G-XXXXXXXXXX`). Le script Analytics ne se charge qu'après acceptation du bandeau — c'est le comportement exigé par le RGPD/ePrivacy (pas de cookie de mesure d'audience avant consentement). Google Search Console n'a pas besoin de cette gestion : il ne dépose pas de cookies côté visiteur, seule la propriété doit être vérifiée dans la console Google (balise meta ou fichier à la racine, à ajouter séparément).

## Infos réelles vs. placeholder

Les coordonnées, l'adresse, les 4 catégories de services (Lettrage, POS, Signalétique visuelle, PLV spectaculaire) et les noms de clients (Quicksilver, Marlboro, Kia, Goodyear Dunlop, Walibi, Cora) viennent du site existant [tagada.be](https://www.tagada.be/Tagada/Home/Home.html).

Restent à vérifier / remplacer avant mise en production :
- Le panneau vidéo du header (actuellement un placeholder avec bouton play) — à remplacer par le vrai showreel de l'atelier.
- Les 6 photos du portfolio (actuellement des placeholders architecturaux du kit Figma) — à remplacer par de vraies photos de réalisations pour chaque client cité.
- Le texte des sections "Pourquoi" / "Atelier" et les réponses de la FAQ sont une proposition éditoriale, pas des faits vérifiés — à valider avec l'équipe Tagada.
