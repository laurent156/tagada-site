# Tagada — Studio créatif

Site vitrine de Tagada SRL, atelier bruxellois de découpe de polystyrène (EPS) et Forex : logos 3D, enseignes, lettrages, PLV et décors sur-mesure.

Site statique en HTML/CSS/JS pur (pas de framework, pas de build), hébergé chez one.com : https://tagada.be/ (copie de travail sur GitHub Pages : https://laurent156.github.io/tagada-site/).

## Mise en ligne

- **Automatique** : chaque push sur `main` déclenche `.github/workflows/deploy-onecom.yml`, qui envoie le site sur one.com en SFTP. Il faut les secrets du dépôt `ONECOM_SFTP_HOST`, `ONECOM_SFTP_USER`, `ONECOM_SFTP_PASSWORD` (et `ONECOM_SFTP_DIR` si le site n'est pas dans le dossier de connexion) ; tant qu'ils manquent, le job ne fait rien.
- **Manuelle** : `python3 scripts/build-deploy.py` rassemble dans `_mise-en-ligne/tagada.be/` uniquement les fichiers utilisés par le site (pages, `.htaccess`, `contact.php`, images référencées…), à déposer à la racine du site.
- **Formulaire** : `contact.php` (PHP `mail()` one.com) envoie les demandes à info@tagada.be, fichiers joints jusqu'à 10 Mo.
- **Redirections** (`.htaccess`) : https + domaine sans www, anciennes pages `/Tagada/...` → nouvelles pages.

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

**Accueil** — sections dans l'ordre : hero (photo Audi Q8, légende au survol), barre de confiance (logos partenaires puis marques, liens vers leurs sites), Savoir-faire (`#pourquoi`), Engagements RSE (`#rse`), Réalisations (`#portfolio`, sélection de 20 pièces filtrables par type + lien vers la galerie complète ; sur mobile, grille 2 colonnes limitée à 8 pièces après filtre — `PORTFOLIO_MOBILE_LIMIT`), Atelier, FAQ, CTA, Contact.

**Réalisations** — galerie complète de 76 projets, classés selon les 4 types du document de légendes : Enseignes In & Out, PLV, Décors, Signalétique visuelle (mêmes filtres sur l'accueil et mêmes options dans le formulaire). Chaque carte affiche titre, description, crédit (@agence/client) et type.

**Pas de "Conditions générales"** : non nécessaires, le site ne vend rien en ligne. Les Mentions légales, elles, sont obligatoires en Belgique pour tout site professionnel.

## Structure

```
index.html, realisations.html, pages légales   # FR à la racine
en/, nl/                                        # traductions (assets en ../)
fonts/                                          # Archivo, Inter Tight (.ttf)
Images/site/                                    # assets utilisés par le site (logos, hero, atelier)
Images/site/realisations/                       # 76 photos de réalisations (NN-titre.jpg, 1200 px max)
Images/Portfolio/                               # photos sources des clients (non utilisées directement)
sitemap.xml, robots.txt, site.webmanifest, favicons
```

Le SEO de base est en place : balises meta/OG, `hreflang`, sitemap, et données structurées JSON-LD (`LocalBusiness` avec services et logo + `FAQPage` sur les accueils, `CollectionPage` listant les 76 projets sur `realisations.html`). Pour le GEO (moteurs IA), `llms.txt` résume le site à la racine.

Toutes les URL absolues (canonical, hreflang, OG, JSON-LD, sitemap, `robots.txt`, `llms.txt`) pointent vers `https://tagada.be/`.

## Cookies & Google Analytics

Un bandeau de consentement (accepter/refuser) est présent sur toutes les pages, avec le choix mémorisé dans `localStorage` (`tagada_consent`). Google Analytics 4 (propriété tagada.be, ID `G-HF92ERQL9P`, variable `GA_MEASUREMENT_ID` dans chaque page) ne se charge qu'après acceptation (exigence RGPD/ePrivacy).

Événements suivis en plus des pages vues : `generate_lead` (formulaire de contact envoyé), `clic_telephone`, `clic_email` (fonction `tagadaTrack`). Penser à passer la conservation des données à 14 mois dans GA (Admin → Collecte et conservation des données → Conservation des données) et à marquer `generate_lead` comme événement clé.

## Reste à faire avant mise en production

- **Showreel** : le panneau vidéo du hero référence `showreel-tagada.mp4`, absent du repo — à ajouter (l'image `hero-video-panel.jpg` sert de poster en attendant).
- **Réseaux sociaux** : les liens Facebook / Instagram / LinkedIn du footer sont masqués (commentés dans le HTML de chaque page) en attendant les vraies URL.
- **Galerie EN/NL** : `realisations.html` n'existe qu'en français ; sur mobile, les accueils EN/NL ont à la place un bouton « Show all / Toon alle » qui déplie les 20 pièces.
- **Textes** : les sections Savoir-faire / Atelier et les réponses de la FAQ sont une proposition éditoriale, à valider avec l'équipe Tagada.
