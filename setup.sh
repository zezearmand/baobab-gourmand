#!/bin/sh
# Installation et configuration complète du site (WP-CLI). À lancer dans le dossier WordPress.
# Variables modifiables : SITE_URL, ADMIN_USER, ADMIN_PASS, ADMIN_EMAIL, MANAGER_PASS, MANAGER_EMAIL
set -eu

SITE_URL="${SITE_URL:-http://localhost:8080}"
ADMIN_USER="${ADMIN_USER:-webmaster}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@baobab-gourmand.example}"
MANAGER_EMAIL="${MANAGER_EMAIL:-gerant@baobab-gourmand.example}"
HERE="$(cd "$(dirname "$0")" && pwd)"
CONTENT="$HERE/content"
rnd() { wp eval 'echo wp_generate_password(18, false);'; }

if ! wp core is-installed 2>/dev/null; then
  ADMIN_PASS="${ADMIN_PASS:-$(php -r 'echo bin2hex(random_bytes(9));')}"
  wp core install --url="$SITE_URL" --title="Le Baobab Gourmand" --admin_user="$ADMIN_USER" \
     --admin_password="$ADMIN_PASS" --admin_email="$ADMIN_EMAIL" --skip-email
  echo "Compte administrateur : $ADMIN_USER / $ADMIN_PASS"
fi
MANAGER_PASS="${MANAGER_PASS:-$(rnd)}"

# --- Langue, réglages généraux
wp language core install fr_FR --activate || true
wp option update blogdescription "Cuisine sénégalaise authentique, généreuse et faite maison"
wp option update timezone_string "Africa/Dakar"
wp option update date_format "j F Y"
wp option update default_comment_status closed
wp option update users_can_register 0
wp rewrite structure '/%postname%/' --hard
wp post delete 1 2 3 --force 2>/dev/null || true
wp config set DISALLOW_FILE_EDIT true --raw 2>/dev/null || true

# --- Thème
wp theme activate baobab-gourmand

# --- Extensions
wp plugin install contact-form-7 flamingo wordpress-seo wordfence wp-super-cache wp-mail-smtp --activate
wp plugin delete hello 2>/dev/null || true

# --- Catégories : Blog > Recettes / Astuces culinaires / Conseils
BLOG=$(wp term create category "Blog" --slug=blog --description="Recettes, astuces et conseils de notre cuisine." --porcelain)
REC=$(wp term create category "Recettes" --slug=recettes --parent="$BLOG" --description="Nos recettes pas à pas." --porcelain)
AST=$(wp term create category "Astuces culinaires" --slug=astuces-culinaires --parent="$BLOG" --description="Les gestes et astuces du chef." --porcelain)
CON=$(wp term create category "Conseils" --slug=conseils --parent="$BLOG" --description="Conseils pour mieux cuisiner, mieux manger et mieux recevoir." --porcelain)

IMG="$(wp theme path)/baobab-gourmand/assets/img"
attach() { # $1=id post  $2=fichier  $3=alt
  AID=$(wp media import "$IMG/$2" --post_id="$1" --featured_image --title="$3" --porcelain)
  wp post meta update "$AID" _wp_attachment_image_alt "$3" >/dev/null
}

# --- Articles de blog
mkpost() { # $1=titre $2=slug $3=fichier $4=catégorie $5=image $6=alt $7=extrait
  PID=$(wp post create "$CONTENT/$3" --post_type=post --post_status=publish --post_title="$1" --post_name="$2" \
        --post_category="$4" --post_excerpt="$7" --porcelain)
  attach "$PID" "$5" "$6"
}
mkpost "Le thieboudienne : la recette familiale pas à pas" recette-thieboudienne recette.html "$REC" blog-recette.jpg "Plat de thieboudienne avec poisson et légumes" "Le plat national du Sénégal, avec le rof, le poisson, les légumes et le riz au bouillon."
mkpost "Réussir le riz du thieb : 4 astuces et le secret du xoon" riz-du-thieb astuce.html "$AST" blog-astuce.jpg "Marmite de riz rouge avec croûte dorée" "Lavage, bouillon, cuisson à couvert et croûte dorée : les astuces de notre cuisine."
mkpost "Bien choisir son poisson au marché" choisir-son-poisson conseil.html "$CON" blog-conseil.jpg "Poissons frais sur un étal de marché" "Yeux, branchies, chair, odeur : nos repères pour acheter du poisson vraiment frais."

# --- Rubriques de la carte (l'ordre de création = ordre d'affichage)
T_MEN=$(wp term create rubrique_carte "Menus" --slug=menus --porcelain)
T_ENT=$(wp term create rubrique_carte "Entrées" --slug=entrees --porcelain)
T_PLA=$(wp term create rubrique_carte "Plats" --slug=plats --porcelain)
T_DES=$(wp term create rubrique_carte "Desserts" --slug=desserts --porcelain)
T_BOI=$(wp term create rubrique_carte "Boissons" --slug=boissons --porcelain)

plat() { # $1=rubrique $2=titre $3=prix (FCFA) $4=description $5=mention $6=image $7=ordre
  PID=$(wp post create --post_type=plat --post_status=publish --post_title="$2" --post_content="$4" --menu_order="$7" --porcelain)
  wp post meta update "$PID" _as_prix "$3" >/dev/null
  if [ -n "$5" ]; then wp post meta update "$PID" _as_badge "$5" >/dev/null; fi
  wp post term set "$PID" rubrique_carte "$1" >/dev/null
  if [ -n "$6" ]; then attach "$PID" "$6" "$2"; fi
}
plat $T_MEN "Menu Déjeuner" "7 500" "Plat du jour + boisson (du mardi au vendredi, midi)." "" "" 1
plat $T_MEN "Menu Teranga" "12 500" "Entrée, plat et dessert au choix à la carte." "" "" 2
plat $T_MEN "Menu Dégustation" "22 000" "Cinq services du chef pour découvrir la cuisine sénégalaise." "Chef" "" 3
plat $T_MEN "Menu Enfant" "4 500" "Poulet grillé ou poisson, riz, jus de fruit et dessert (jusqu'à 10 ans)." "" "" 4
plat $T_ENT "Pastels au poisson" "3 500" "Beignets croustillants farcis au poisson, sauce tomate pimentée." "Signature" pastels.jpg 1
plat $T_ENT "Salade de mangue verte et crevettes" "4 500" "Crevettes sautées, mangue verte, citron vert et menthe." "" mangue.jpg 2
plat $T_ENT "Velouté de patate douce" "3 500" "Gingembre frais et lait de coco, pain grillé." "Végétarien" patate.jpg 3
plat $T_PLA "Thieboudienne" "7 500" "Le plat national : riz au poisson, chou, carotte, manioc et aubergine." "Signature" thieb.jpg 1
plat $T_PLA "Yassa au poulet" "6 500" "Poulet mariné au citron et oignons confits, riz blanc." "Signature" yassa.jpg 2
plat $T_PLA "Mafé de bœuf" "7 000" "Mijoté de bœuf à la sauce d'arachide, légumes et riz." "" mafe.jpg 3
plat $T_PLA "Dibi d'agneau" "8 500" "Agneau grillé au feu de bois, oignons et moutarde, pain." "Signature" dibi.jpg 4
plat $T_PLA "Mafé de légumes" "5 500" "Patate douce, carotte et chou dans une sauce d'arachide." "Végétarien" mafelegumes.jpg 5
plat $T_DES "Thiakry" "2 500" "Couscous de mil, lait caillé sucré, vanille et raisins secs." "" thiakry.jpg 1
plat $T_DES "Crème de bouye" "3 000" "Dessert onctueux au fruit du baobab (pain de singe)." "Signature" bouye.jpg 2
plat $T_DES "Salade de fruits tropicaux" "2 500" "Mangue, ananas, papaye et menthe fraîche." "" fruits.jpg 3
plat $T_BOI "Bissap glacé" "1 500" "Infusion de fleurs d'hibiscus, menthe, sucre de canne." "" bissap.jpg 1
plat $T_BOI "Jus de gingembre" "1 500" "Jus pressé, citron et gingembre frais (gnamakoudji)." "Épicé" gingembre.jpg 2
plat $T_BOI "Jus de bouye" "2 000" "Fruit du baobab, lait et sucre." "" bouyejus.jpg 3
plat $T_BOI "Café Touba" "1 000" "Café aromatisé au poivre de Guinée (djar)." "" touba.jpg 4

# --- Pages
HOME=$(wp post create "$CONTENT/histoire.html" --post_type=page --post_status=publish --post_title="Accueil" --post_name=accueil --porcelain)
CARTE=$(wp post create --post_type=page --post_status=publish --post_title="Notre carte" --post_name=carte \
        --post_content="Des produits frais, de saison, cuisinés chaque jour. Retrouvez nos menus, entrées, plats, desserts et boissons." --porcelain)
wp post meta update "$CARTE" _wp_page_template page-carte.php >/dev/null

# Formulaire de contact (Contact Form 7 : champs nom, e-mail, sujet, message)
FID=$(wp post list --post_type=wpcf7_contact_form --field=ID --posts_per_page=1 | head -n1)
if [ -z "$FID" ]; then FID=$(wp post create --post_type=wpcf7_contact_form --post_status=publish --post_title="Contact" --porcelain); fi
wp post update "$FID" --post_title="Contact" >/dev/null
wp post meta update "$FID" _form '<label>Nom *
[text* your-name autocomplete:name]</label>

<label>E-mail *
[email* your-email autocomplete:email]</label>

<label>Sujet *
[text* your-subject]</label>

<label>Message *
[textarea* your-message]</label>

[submit "Envoyer le message"]' >/dev/null
MAIL_TO="${MAIL_TO:-$ADMIN_EMAIL}" FID="$FID" wp eval '
$id = (int) getenv("FID");
$m = get_post_meta($id, "_mail", true);
if (!is_array($m)) { $m = array(); }
$m["recipient"] = getenv("MAIL_TO");
$m["subject"]   = "[Baobab Gourmand] [your-subject]";
$m["sender"]    = "[_site_title] <wordpress@" . wp_parse_url(home_url(), PHP_URL_HOST) . ">";
$m["body"]      = "De : [your-name] <[your-email]>\nSujet : [your-subject]\n\nMessage :\n[your-message]\n\n-- \nMessage envoyé depuis le formulaire de contact du site.";
$m["additional_headers"] = "Reply-To: [your-email]";
$m["use_html"]  = false;
update_post_meta($id, "_mail", $m);
$msg = get_post_meta($id, "_messages", true);
if (is_array($msg)) {
  $msg["mail_sent_ok"] = "Merci, votre message a bien été envoyé. Nous vous répondons rapidement.";
  $msg["validation_error"] = "Un ou plusieurs champs sont invalides. Merci de vérifier et réessayer.";
  update_post_meta($id, "_messages", $msg);
}'
CONTACT=$(wp post create --post_type=page --post_status=publish --post_title="Contact" --post_name=contact \
        --post_content="[contact-form-7 id=\"$FID\" title=\"Contact\"]" --porcelain)
wp post meta update "$CONTACT" _wp_page_template page-contact.php >/dev/null

wp option update show_on_front page
wp option update page_on_front "$HOME"

# --- Menus de navigation
MENU=$(wp menu create "Menu principal" --porcelain)
wp menu item add-post "$MENU" "$HOME" --title="Accueil" >/dev/null
wp menu item add-post "$MENU" "$CARTE" --title="Notre carte" >/dev/null
BI=$(wp menu item add-term "$MENU" category "$BLOG" --title="Blog" --porcelain)
wp menu item add-term "$MENU" category "$REC" --parent-id="$BI" >/dev/null
wp menu item add-term "$MENU" category "$AST" --parent-id="$BI" >/dev/null
wp menu item add-term "$MENU" category "$CON" --parent-id="$BI" >/dev/null
wp menu item add-post "$MENU" "$CONTACT" --title="Contact" >/dev/null
wp menu location assign "$MENU" primary
FM=$(wp menu create "Pied de page" --porcelain)
wp menu item add-post "$FM" "$CARTE" --title="Notre carte" >/dev/null
wp menu item add-post "$FM" "$CONTACT" --title="Contact" >/dev/null
wp menu location assign "$FM" footer

# --- Compte gestionnaire (rôle Éditeur : pages, articles, carte, menus ; pas d'accès aux extensions/réglages)
wp cap add editor edit_theme_options
wp user create gerant "$MANAGER_EMAIL" --role=editor --display_name="Responsable du restaurant" --user_pass="$MANAGER_PASS" || true

# --- Cache
wp super-cache enable 2>/dev/null || true
wp rewrite flush --hard 2>/dev/null || wp rewrite flush

echo "------------------------------------------------------------"
echo "Site prêt : $SITE_URL"
echo "Gestionnaire : gerant / $MANAGER_PASS  (rôle Éditeur)"
echo "Pensez à configurer wp-mail-smtp et à relire les réglages Wordfence / Yoast."
