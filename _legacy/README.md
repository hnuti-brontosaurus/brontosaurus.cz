# Brontoweb

Tento repozitář obsahuje celé chování brontowebu.
Web běží na redakčním systému Wordpress, takže veškerá logika i UI
je uděláno formou rozšíření. Protože logika je relativně dost svázána
s šablonou samotnou, je to také tak implementováno – pomocí
jedné šablony pro Wordpress.

Brontoweb byl původně z důvodu rychlého zveřejnění nové verze nakódován
víceméně staticky (vyjma komunikace s BISem), redakční systém jako takový
se používá spíše méně. Vize je přesunout veškerý obsah
do Wordpressu tak, aby byl administrovatelný, ale zároveň se nedaly rozbít
základní designové koncepty webu.

Statický kód je momentálně napsán jako template parts.


## Vývoj

Šablona je vyvíjena standardními webovými jazyky – HTML+CSS+JS pro frontend, PHP pro backend.
Některé vrstvy obsahují nástroje pro usnadnění práce vývojáře.

### Backend

Šablona využívá pár knihoven třetích stran. Pro správu těchto knihoven je použit Composer (světový standard pro správu knihoven v PHP), který umožňuje snadnou správu a aktualizaci tzv. závislostí. Composer je ke stažení [zde](https://getcomposer.org/) a po jeho instalaci lze používat standardně přes příkazovou řádku (viz [Instalace](#Instalace)).

### Frontend

Frontend se v šabloně skládá z pěti různých zdrojů:
- šablony
- styly
- skripty
- obrázky
- fonty

Šablony se zpracovávají PHP skriptem a vypisují na výstup.

Styly jsou psány jazykem SCSS, tedy před použitím v prohlížeči se musí vždy přeložit preprocesorem SASS. Použití preprocesoru usnadňuje psaní CSS – např. použití proměnných pro opakované hodnoty (`$var: 20rem`), řetězení opakujících se slov v selektorech (`&__item`) etc.

Skripty jsou psány standardně v Javascriptu a jsou ve složce `scripts`.

Obrázky se nahrávají na produkční verzi Brontowebu a odtud se linkují.

S fonty se nic nedělá.

Aby se nemusela každá sada zdrojů zpracovávat samostatně a ručně, využívá šablona nástroje Gulp, který umožňuje zpracovávat více úloh dohromady. Gulp pracuje podle konfigurace uložené v souboru `gulpfile.babel.js`. Gulp je de facto další javascriptový program, proto je potřeba mít nainstalovaný Node.js (program umožňující spouštět javascript jako aplikaci v terminálu) a yarn (správce balíčků pro Node.js).

### Shrnutí

Ve finále je třeba mít v počítači vždy dvojici interpreter+správce balíčků pro backend a pro frontend. Pro backend je to PHP+Composer, pro frontend Node.js+yarn.

## Instalace

Pro zprovozonění šablony pro vývoj je třeba šablonu umístit do adresáře `wp-content/themes/brontosaurus`, nainstalovat backend i frontend závislosti a potom šablonu ve WP aktivovat.

```bash
# nainstaluje všechny knihovny třetích stran pro backend
composer install

# nainstaluje všechny balíčky třetích stran pro frontend (včetně Gulp)
yarn install
```

```bash
# spustí úlohy v Gulpu pro vývojový režim –
# při každé změně znovu zpracuje změněné zdroje,
# aby byly vývojáři hned dostupné v prohlížeči
yarn dev

# zpracuje zdroje do co nejefektivnější podoby pro nasazení na ostrý web
# typicky součást buildu v CI
NODE_ENV=production yarn build
```

> Poznámka: build přes gulp se v současnosti týká už jen stylů, pro zbytek zdrojů již není potřeba jej pouštět.