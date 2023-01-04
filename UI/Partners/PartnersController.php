<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\Partners;

use HnutiBrontosaurus\Theme\UI\Base\Base;
use HnutiBrontosaurus\Theme\UI\Controller;
use Latte\Engine;


final class PartnersController implements Controller
{

	public function __construct(
		private Base $base,
		private Engine $latte,
	) {}


	public function render(): void
	{
		$objectify = static fn(array $logos) => \array_map(
			static fn(array $logo): object => (object) $logo,
			$logos,
		);

		$params = [
			'partners' => $objectify(self::partners()),
			'medialPartners' => $objectify(self::medialPartners()),
			'cooperating' => $objectify(self::cooperating()),
			'membersOf' => $objectify(self::membersOf()),
		];

		$this->latte->render(
			__DIR__ . '/PartnersController.latte',
			\array_merge($this->base->getLayoutVariables('partners'), $params),
		);
	}

	private static function getPath(string $fileName): string
	{
		return get_template_directory_uri() . '/UI/Partners/logos/' . $fileName;
	}

	/** @return array<array{id: string, name: string, image: string, url: string}> */
	private static function partners(): array
	{
		return [
			[
				'id' => 'mzp',
				'name' => 'Ministerstvo životního prostředí',
				'image' => self::getPath('ministerstvo-zivotniho-prostredi.png'),
				'url' => 'https://www.mzp.cz',
			],
			[
				'id' => 'msmt',
				'name' => 'Ministerstvo školství, mládeže a tělovýchovy',
				'image' => self::getPath('ministerstvo-skolstvi.png'),
				'url' => 'http://www.msmt.cz',
			],
			[
				'id' => 'mv',
				'name' => 'Ministerstvo vnitra České republiky',
				'image' => self::getPath('ministerstvo-vnitra.png'),
				'url' => 'https://www.mvcr.cz',
			],
			[
				'id' => 'mzv',
				'name' => 'Ministerstvo zahraničních věcí České republiky',
				'image' => self::getPath('ministerstvo-zahranici.png'),
				'url' => 'https://www.mzv.cz',
			],
			[
				'id' => 'sfzp',
				'name' => 'Státní fond životního prostředí České republiky',
				'image' => self::getPath('statni-fond.png'),
				'url' => 'https://www.sfzp.cz',
			],
			[
				'id' => 'erasmus',
				'name' => 'Erasmus+',
				'image' => self::getPath('erasmus.png'),
				'url' => 'http://www.erasmusprogramme.com',
			],
			[
				'id' => 'jmk',
				'name' => 'Jihomoravský kraj',
				'image' => self::getPath('jihomoravsky-kraj.png'),
				'url' => 'https://www.jmk.cz/',
			],
			[
				'id' => 'brno',
				'name' => 'Brno',
				'image' => self::getPath('brno.png'),
				'url' => 'https://www.brno.cz',
			],
			[
				'id' => 'praha',
				'name' => 'Praha',
				'image' => self::getPath('praha.png'),
				'url' => 'http://www.praha.eu',
			],
			[
				'id' => 'pk',
				'name' => 'Pardubický kraj',
				'image' => self::getPath('pardubicky-kraj-logo.jpg'),
				'url' => 'https://www.pardubickykraj.cz/',
			],
			[
				'id' => 'muni',
				'name' => 'Masarykova univerzita',
				'image' => self::getPath('muni.jpg'),
				'url' => 'https://www.muni.cz/',
			],
			[
				'id' => 'partnerstvi',
				'name' => 'Nadace partnerství lidé a příroda',
				'image' => self::getPath('partnerstvi.png'),
				'url' => 'https://www.nadacepartnerstvi.cz',
			],
			[
				'id' => 'stromy',
				'name' => 'Stromy svobody 1918 až 2018',
				'image' => self::getPath('stromy-svobody.png'),
				'url' => 'https://www.stromysvobody.cz',
			],
			[
				'id' => 'brnoSever',
				'name' => 'Brno sever',
				'image' => self::getPath('brno-sever.png'),
				'url' => 'http://www.sever.brno.cz/',
			],
			[
				'id' => 'esc',
				'name' => 'European solidarity corps',
				'image' => self::getPath('european-solidarity-corps.jpg'),
				'url' => 'https://europa.eu/youth/solidarity_cs',
			],
			[
				'id' => 'veronica',
				'name' => 'Nadace Veronica',
				'image' => self::getPath('nadace-veronika.jpg'),
				'url' => 'https://nadace.veronica.cz/',
			],
			[
				'id' => 'grifart',
				'name' => 'GRIFART, spol. s r.o.',
				'image' => self::getPath('grifart.png'),
				'url' => 'https://grifart.cz',
			],
			[
				'id' => 'smartlook',
				'name' => 'Smartlook',
				'image' => self::getPath('smartlook.png'),
				'url' => 'https://www.smartlook.com/cs',
			],
			[
				'id' => 'osf',
				'name' => 'Nadace OSF',
				'image' => self::getPath('nadace-osf.png'),
				'url' => 'https://osf.cz',
			],
			[
				'id' => 'acf',
				'name' => 'Active Citizens Fund',
				'image' => self::getPath('active-citizens-fund.jpg'),
				'url' => 'https://activecitizensfund.no',
			],
		];
	}

	/** @return array<array{id: string, name: string, image: string, url: string|null}> */
	private static function medialPartners(): array
	{
		return [
			[
				'id' => 'dobrovolnik',
				'name' => 'Dobrovolník.cz',
				'image' => self::getPath('dobrovolnik.png'),
				'url' => 'https://www.dobrovolnik.cz',
			],
			[
				'id' => 'm',
				'name' => 'm!',
				'image' => self::getPath('m.png'),
				'url' => null,
			],
			[
				'id' => 'prostredoskolaky',
				'name' => 'ProStředoškoláky',
				'image' => self::getPath('pro-stredoskolaky.png'),
				'url' => 'http://www.prostredoskolaky.cz',
			],
			[
				'id' => 'signaly',
				'name' => 'signály.cz',
				'image' => self::getPath('signaly.png'),
				'url' => 'https://www.signaly.cz',
			],
		];
	}

	/** @return array<array{id: string, name: string, image: string, url: string|null}> */
	private static function cooperating(): array
	{
		return [
			[
				'id' => 'nasePriroda',
				'name' => 'Naše příroda',
				'image' => self::getPath('nase-priroda.svg'),
				'url' => 'http://www.nasepriroda.cz',
			],
			[
				'id' => 'propamatky',
				'name' => 'Pro památky, portál – časopis',
				'image' => self::getPath('pro-pamatky.png'),
				'url' => 'http://www.propamatky.info',
			],
			[
				'id' => 'mindless',
				'name' => 'Mindless',
				'image' => self::getPath('mindless.png'),
				'url' => null,
			],
		];
	}

	/** @return array<array{id: string, name: string, image: string, url: string}> */
	private static function membersOf(): array
	{
		return [
			[
				'id' => 'yee',
				'name' => 'YEE',
				'image' => self::getPath('yee.png'),
				'url' => 'https://yeenet.eu',
			],
			[
				'id' => 'zelenykruh',
				'name' => 'Zelený kruh, asociace ekologických organizací',
				'image' => self::getPath('zeleny-kruh.png'),
				'url' => 'http://www.zelenykruh.cz',
			],
			[
				'id' => 'crdm',
				'name' => 'Česká rada dětí a mládeže',
				'image' => self::getPath('ceska-rada-deti.png'),
				'url' => 'http://crdm.cz',
			],
			[
				'id' => 'zasnadnedarcovstvi',
				'name' => 'Za snadné dárcovství',
				'image' => self::getPath('snadne-darcovstvi.png'),
				'url' => 'http://www.snadnedarcovstvi.cz',
			],
			[
				'id' => 'kppo',
				'name' => 'Koalice proti palmovému oleji',
				'image' => self::getPath('proti-palmovemu-oleji.png'),
				'url' => 'http://www.stoppalmovemuoleji.cz',
			],
			[
				'id' => 'kk',
				'name' => 'Klimatická koalice',
				'image' => self::getPath('klimaticka-koalice.png'),
				'url' => 'https://klimatickakoalice.cz',
			],
		];
	}

}
