# Tagada — Studio créatif

Site vitrine de Tagada SRL, atelier bruxellois de découpe de polystyrène (EPS) et Forex : logos 3D, enseignes, lettrages, PLV et décors sur-mesure.

Site statique en HTML/CSS/JS pur (pas de framework, pas de build), déployé sur GitHub Pages : https://laurent156.github.io/tagada-site/

## Contexte

Traduction d'une maquette Figma (Relume Kit) dans le design system extrait du template Webflow [orange-template.webflow.io](https://orange-template.webflow.io/) : typographie (Archivo / Inter Tight), rayons, easing, et composants (accordéon FAQ à "rideau", menu overlay avec ancres, panneau vidéo type showreel, hover sur les cartes projet). Suite à la réunion client, la palette orange a été remplacée par une identité rouge (logo Tagada rouge, boutons et section Engagements en rouge).

Le design system est documenté comme skill réutilisable dans `.claude/skills/orange-design-system/` (tokens CSS + composants).

## Pages

| Page | FR | EN | NL |
|---|---|---|---|
| Accueil | `index.html` | `en/index.html` | `nl/index.html` |
| Toutes les réalisations | `realisations.html` | — | — |
| Mentions légales | `mentions-legales.html` | `en/…` | `nl/…` |
| Confidentialité (RGPD) | `confidentialite.html` | `en/…` | `nl/…` |
| Cookies | `cookies.html` | `en/…` | `nl/…` |

Le sélecteur de langue se trouve dans le menu overlay.

**Accueil** — sections dans l'ordre : hero (photo Audi Q8, légende au survol), barre de confiance (logos partenaires puis marques, liens vers leurs sites), Savoir-faire (`#pourquoi`), Engagements RSE (`#rse`), Réalisations (`#portfolio`, 30 pièces + lien vers la galerie complète ; sur mobile, grille 2 colonnes limitée à 8 pièces après filtre — `PORTFOLIO_MOBILE_LIMIT`), Atelier, FAQ, CTA, Contact.

**Réalisations** — galerie complète de 72 photos clients.

**Pas de "Conditions générales"** : non nécessaires, le site ne vend rien en ligne. Les Mentions légales, elles, sont obligatoires en Belgique pour tout site professionnel.

## Structure

```
index.html, realisations.html, pages légales   # FR à la racine
en/, nl/                                        # traductions (assets en ../)
fonts/                                          # Archivo, Inter Tight (.ttf)
Images/site/                                    # assets utilisés par le site (logos, hero, atelier)
Images/site/portfolio2/                         # 72 photos de réalisations
Images/Portfolio/                               # photos sources des clients (non utilisées directement)
sitemap.xml, robots.txt, site.webmanifest, favicons
```

Le SEO de base est en place : balises meta/OG, données structurées JSON-LD, `hreflang`, sitemap.

## Cookies & Google Analytics

Un bandeau de consentement (accepter/refuser) est présent sur toutes les pages, avec le choix mémorisé dans `localStorage` (`tagada_consent`). Tant que Google Analytics n'a pas d'ID, le bandeau s'affiche et fonctionne mais ne charge aucun script.

**Pour brancher Google Analytics** : dans chaque page HTML (FR, EN, NL), chercher la ligne
```js
var GA_MEASUREMENT_ID = ''; // TODO : coller l'ID Google Analytics (format G-XXXXXXXXXX) une fois le compte créé
```
et y coller l'ID de mesure. Le script ne se charge qu'après acceptation du bandeau (exigence RGPD/ePrivacy). Google Search Console ne dépose pas de cookies : seule la vérification de propriété est à ajouter (balise meta ou fichier à la racine).

## Reste à faire avant mise en production

- **Showreel** : le panneau vidéo du hero référence `showreel-tagada.mp4`, absent du repo — à ajouter (l'image `hero-video-panel.jpg` sert de poster en attendant).
- **Formulaire de contact** : non fonctionnel (`onsubmit="return false;"`) — à brancher sur un service d'envoi (Formspree, Netlify Forms, etc.).
- **Google Analytics** : ID de mesure à renseigner (voir ci-dessus).
- **Réseaux sociaux** : les liens Facebook / Instagram / LinkedIn du footer sont masqués (commentés dans le HTML de chaque page) en attendant les vraies URL.
- **Galerie EN/NL** : `realisations.html` n'existe qu'en français ; sur mobile, les accueils EN/NL ont à la place un bouton « Show all / Toon alle » qui déplie les 30 pièces.
- **Textes** : les sections Savoir-faire / Atelier et les réponses de la FAQ sont une proposition éditoriale, à valider avec l'équipe Tagada.
