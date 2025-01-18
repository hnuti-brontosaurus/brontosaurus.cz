<?php

use HnutiBrontosaurus\BisClient\AdministrationUnit\Response\AdministrationUnit;
use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\DataContainers\OrganizationalUnitDC;

/** @var Container $hb_container defined in functions.php */
$hb_bisApiClient = $hb_container->getBisClient();

$hasBeenUnableToLoad = false;

try {
    // get all organizational units
    $units = $hb_bisApiClient->getAdministrationUnits();

    // filter only base units and clubs
    $units = array_filter(
        $units,
        static fn(AdministrationUnit $unit): bool
            => $unit->isBaseUnit() || $unit->isClub()
    );

    // transfer DTOs to DCs
    $units = array_map(
        static fn(AdministrationUnit $unit): OrganizationalUnitDC
            => OrganizationalUnitDC::fromDTO($unit),
        $units,
    );

} catch (ConnectionToBisFailed) {
    $hasBeenUnableToLoad = true;
}

?><main role="main">
	<article class="baseUnits hb-mw-40 hb-mi-auto">
		<h1>
			Seznam základních článků a klubů
		</h1>

		<?php if ($hasBeenUnableToLoad): ?>
			<div class="noResults">
				Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o základních článcích.
				Zkuste to prosím za chvilku znovu.
			</div>

		<?php else: ?>
			<ul class="hb-lst-n">
                <?php
                $counter = 0;
                foreach ($units as $unit):
                ?>
				<li class="<?php if ($counter < (count($units) - 1)): ?>hb-mbe-5<?php endif; ?>">
					<h2 class="hb-ta-l"><?php echo $unit->name ?></h2>

					<div class="baseUnits__contact">
						<a class="baseUnits__address hb-pis-4" href="https://mapy.cz/?q=<?php echo $unit->address ?>" rel="noopener noreferrer" target="_blank">
							<?php echo $unit->address ?>
						</a>
					</div>

                    <?php if ($unit->website !== null): ?>
					<div class="baseUnits__contact">
						<a class="baseUnits__website hb-pis-4" href="<?php echo $unit->website ?>" rel="noopener noreferrer" target="_blank"><?php echo $unit->website ?></a>
					</div>
                    <?php endif; ?>

                    <?php if ($unit->emailAddress !== null): ?>
					<div class="baseUnits__contact">
						<a class="baseUnits__emailAddress hb-pis-4" href="mailto:<?php echo $unit->emailAddress ?>" rel="noopener noreferrer" target="_blank"><?php echo $unit->emailAddress ?></a>
					</div>
                    <?php endif; ?>
				</li>
                <?php
                $counter++;
                endforeach;
                ?>
			</ul>
		<?php endif; ?>
	</article>
</main>
