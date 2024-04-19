<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\SupportOverview;

use HnutiBrontosaurus\Theme\UI\AboutSuccesses\AboutSuccessesController;
use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;
use stdClass;
use function sprintf;
use function wp_get_theme;


final class SupportOverviewController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		add_action('wp_enqueue_scripts', function () {
			$theme = wp_get_theme();
			$themeVersion = $theme->get('Version');
			wp_enqueue_script('brontosaurus-supportOverview-references', $theme->get_template_directory_uri() . '/frontend/dist/js/references.js', [], $themeVersion);
		});

		$params = [
			'whatWeDo' => self::whatWeDo(),
			'aboutSuccessesLink' => $this->base->getLinkFor(AboutSuccessesController::PAGE_SLUG),
			'stories' => self::stories(),
			'references' => self::references(),
		];

		$this->latte->render(
			__DIR__ . '/SupportOverviewController.latte',
			\array_merge($this->base->getLayoutVariables('supportoverview'), $params),
		);
	}

	/** @return array<stdClass{title: string, text: string[], image: string}> */
	private static function whatWeDo(): array
	{
		return [
			(object) [
				'title' => 'Za živou krajinu',
				'text' => [
					'Chceme, aby naše krajina čelící suchu a ztrátě pestrosti byla zdravá a odolala změnám klimatu.',
					'Sázíme stromořadí, oživujeme polní krajinu, staráme se o mokřady, budujeme lužní park Krče. Chceme s dobrovolníky a dobrovolnicemi obnovit další místa po celé ČR. Podpoř nás v tom.',
				],
				'image' => self::imagePathFor('adopce-krajina'),
			],
			(object) [
				'title' => 'Pro památky',
				'text' => [
					'Pečujeme o desítky historických památek tvořících naše kulturní dědictví, například o hrad Lukov. Probouzíme o ně zájem u mladých lidí i veřejnosti.',
					'Chceme v tom pokračovat a opravovat i méně známé památky u nás i u krajanů v Banátu. Podpoř nás, ať můžeme pomáhat na dalších lokalitách.',
				],
				'image' => self::imagePathFor('adopce-pamatky'),
			],
			(object) [
				'title' => 'Chráníme vše živé',
				'text' => [
					'Ohroženy jsou nejen chráněné druhy rostlin a živočichů, ale ubývá i těch donedávna hojných. Chceme, aby příroda zůstala pestrá i bohatá.',
					'Budujeme úkryty, vyvěšujeme budky i netopýrníky, vysazujeme květnaté louky, staráme se o migrující žáby. Podpoř nás při tvorbě prostředí plného ptačího zpěvu a hmyzího bzukotu.',
				],
				'image' => self::imagePathFor('adopce-zivot'),
			],
			(object) [
				'title' => 'Práce na chráněných územích',
				'text' => [
					'Česká republika oplývá vzácnými přírodními lokalitami, které je třeba chránit pro budoucí generace.',
					'Snažíme se přispět k jejich zachování. Péčí o orchidejové louky, udržováním stepních společenstev či  bojem s invazními druhy. Podpoř zapojení dalších mladých lidí do těchto aktivit!',
				],
				'image' => self::imagePathFor('adopce-uzemi'),
			],
			(object) [
				'title' => 'Pro další generace',
				'text' => [
					'Mladí lidé pociťují obavy z budoucnosti a často nevidí, jak něco změnit. Otevíráme jim cestu do komunit přátel se kterými mnohé zmůžou.',
					'Na výukových programech v přírodě jim ukazujeme, jak mohou životní prostředí chránit. Podpoř aktivity pro mládež, která se chce podílet na naší budoucnosti.',
				],
				'image' => self::imagePathFor('adopce-generace'),
			],
		];
	}

	/**
	 * @return array<stdClass{image: string, title: string, text: string}>
	 */
	private static function stories(): array
	{
		return [
			(object) [
				'title' => 'Spolu po tornádu',
				'text' => 'Po tornádu se nám podařilo obnovit stovky dalších stromů a zeleně. Navíc jsme opravili zničenou dětskou klubovnu v Mikulčicíc nově vylepšenou o ekologická opatření.',
				'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/01/IMGP2396-scaled.jpg',
				'link' => 'https://brontosaurus.cz/pribehy-nadseni/spolu-po-tornadu/',
			],
			(object) [
				'title' => 'Malé Česko v Banátu - přátelství skrze pomoc',
				'text' => 'Brontosauři ve zdejších obcích s významnou českou komunitou pravidelně čistí veřejná prostranství, opravují památky, pečují o přírodu a rozvíjejí šetrnou turistiku skrze mapování turistických tras.',
				'image' => 'https://brontosaurus.cz/wp-content/uploads/2024/01/rumunsko-mala.jpg',
				'link' => 'https://brontosaurus.cz/pribehy-nadseni/male-cesko-v-banatu/',
			],
			(object) [
				'title' => 'Kudlačena, aneb stále jsme to nevzdali',
				'text' => 'Brontosauři kolem péče o Kudlačenu zformovali základní článek Kolovrátek, se kterým se dlouhodobě věnují pravidelnému kosení a hrabání pro zachování pestrosti krajiny a ochranu vzácných druhů.',
				'image' => 'https://brontosaurus.cz/wp-content/uploads/2023/09/kudlacena-14-scaled.jpg',
				'link' => 'https://brontosaurus.cz/pribehy-nadseni/kudlacena/',
			],
		];
	}


	/** @return array<stdClass{title: string, text: string, image: string}> */
	private static function references(): array
	{
		return [
			(object) [
				'name' => 'Zdeněk Frélich',
				'role' => 'poradce pro oblast životního prostředí',
				'quotation' => 'Adoptoval jsem Brontosaura, protože chci, aby byl nezávislý.',
				'image' => self::imagePathFor('supportOverview-supporters-frelich'),
			],
			(object) [
				'name' => 'Zuzana „Zula“ Brzobohatá',
				'role' => 'bývalá europoslankyně',
				'quotation' => 'Vracím brontosaurům to, co mi dali: umět se postavit čelem k věcem, organizovat akce pro druhé a vidět svět jinýma očima.',
				'image' => self::imagePathFor('supportOverview-supporters-brzobohata'),
			],
			(object) [
				'name' => 'Martin Perlík',
				'role' => 'stavební inženýr a projektant',
				'quotation' => 'Hodně let jsem organizoval brontosauří akce. Teď už jsem z Brontosaura vyrostl a nastal čas, aby se snažili mladší. Adopce Brontosaura je pro mne příležitost, jak s nimi udržet kontakt.',
				'image' => self::imagePathFor('supportOverview-supporters-perlik'),
			],
			(object) [
				'name' => 'Pepa Hladký',
				'role' => 'cestovatel, vodák a organizátor akcí',
				'quotation' => 'Když jsem se stal organizátorem brontosauřích táborů a víkendovek, uvědomil jsem si, kolik je za tím tvrdé dřiny. Proto jej chci podporovat.',
				'image' => self::imagePathFor('supportOverview-supporters-hladky'),
			],
			(object) [
				'name' => 'Tibor Vansa',
				'role' => 'předseda Pirátů na Praze 4',
				'quotation' => 'Brontíci mi dali šanci zapadnout a zároveň být sám sebou, potkat lidi s podobnými ideály. Chtěl bych, aby tuhle partu mohli zažít i mladší generace.',
				'image' => self::imagePathFor('supportOverview-supporters-vansa'),
			],
			(object) [
				'name' => 'Roman Zemánek',
				'role' => 'ředitel domu dětí a mládeže Strážnice',
				'quotation' => 'Brontosaurus je moje srdeční záležitost, ale i rozum mi říká, že co je dobré a má smysl pro lidi a přírodu, si zaslouží moji podporu.',
				'image' => self::imagePathFor('supportOverview-supporters-zemanek'),
			],
		];
	}

	private static function imagePathFor(string $imageName): string
	{
		$theme = wp_get_theme();
		return sprintf('%s/frontend/dist/images/%s.jpg', $theme->get_template_directory_uri(), $imageName);
	}

}
