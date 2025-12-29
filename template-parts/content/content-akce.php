<?php

use HnutiBrontosaurus\BisClient\BisClient;
use HnutiBrontosaurus\BisClient\ConnectionToBisFailed;
use HnutiBrontosaurus\BisClient\EventNotFound;
use HnutiBrontosaurus\Theme\Container;
use HnutiBrontosaurus\Theme\NotFound;
use HnutiBrontosaurus\Theme\DataContainers\Events\EventDC;
use Latte\Engine;
use Nette\Utils\Strings;
use Tracy\Debugger;

/** @var Container $hb_container defined in functions.php */

$hb_bisApiClient = $hb_container->getBisClient();
$hb_dateFormatForHuman = $hb_container->getDateFormatForHuman();
$hb_dateFormatForRobot = $hb_container->getDateFormatForRobot();
$hb_applicationUrlTemplate = $hb_container->getConfiguration()->get('bis:applicationUrlTemplate');

function hb_formatPhoneNumber(string $phoneNumber): string
{
	// todo use brick/phone instead?
	switch (\mb_strlen($phoneNumber)) {
		case 9: // 123456789
			return \sprintf('%s %s %s', \mb_substr($phoneNumber, 0, 3), \mb_substr($phoneNumber, 3, 3), \mb_substr($phoneNumber, 6, 3));
		case 11: // 123 456 789
			return $phoneNumber;
		case 13: // +420123456789
			return \sprintf('%s %s %s %s', \mb_substr($phoneNumber, 0, 4), \mb_substr($phoneNumber, 4, 3), \mb_substr($phoneNumber, 7, 3), \mb_substr($phoneNumber, 10, 3));
		case 16: // +420 123 456 789
			return $phoneNumber;
		case 14: // 00420123456789
			return \sprintf('+%s %s %s %s', \mb_substr($phoneNumber, 2, 3), \mb_substr($phoneNumber, 5, 3), \mb_substr($phoneNumber, 8, 3), \mb_substr($phoneNumber, 11, 3));
		case 17: // 00420 123 456 789
		case 18: // 00 420 123 456 789
			return \str_replace(['00 ', '00'], '+', $phoneNumber);
	}

	return $phoneNumber; // fallback any other format
}

function hb_renderWebsiteUserFriendly(string $website): string {
    $hostname = parse_url($website, PHP_URL_HOST);
    $hostname = $hostname !== null ? $hostname : $website; // in case of passing "a.b.cz" to parse_url(), it fails to parse it for some reason, so just skip it

    if (Strings::startsWith($hostname, 'www.')) {
        $hostname = str_replace('www.', '', $hostname);
    }

    return $hostname;
}

function hb_formatDayCount(int $days): string {
    return match($days) {
        1 => sprintf('%d den', $days),
        2,3,4 => sprintf('%d dny', $days),
        default => sprintf('%d dní', $days),
    };
}

function hb_formatHourCount(int $hours): string {
    return match($hours) {
        1 => sprintf('%d hodinu', $hours),
        2,3,4 => sprintf('%d hodiny', $hours),
        default => sprintf('%d hodin', $hours),
    };
}

$eventId = (int) get_query_var('eventId');

// fix rank math tags
// remove generated meta data on this page, see https://support.rankmath.com/ticket/is-there-anyway-to-disable-or-remove-specific-meta-from-posts/
// https://rankmath.com/kb/filters-hooks-api-developer/#change-the-title
add_filter('rank_math/frontend/title', static fn($title) => null);
// https://rankmath.com/kb/filters-hooks-api-developer/#remove-opengraph-tags
add_action('rank_math/head', function () {
    remove_all_actions('rank_math/opengraph/facebook');
    remove_all_actions('rank_math/opengraph/twitter');
});

try {
    $hasBeenUnableToLoad = false;
    $event = $hb_bisApiClient->getEvent($eventId);
    $eventDC = new EventDC($event, $hb_dateFormatForHuman, $hb_dateFormatForRobot);

    // add event name to title tag (source https://stackoverflow.com/a/62410632/3668474)
    add_filter(
        'document_title_parts',
        fn(array $title) => array_merge($title, ['title' => $event->getName()]),
    );

} catch (EventNotFound) {
    throw new NotFound();

} catch (ConnectionToBisFailed $e) {
    $hasBeenUnableToLoad = true;
    $eventDC = null;
	Debugger::log($e);
}

$event = $eventDC;

function hb_detail_application(EventDC $event, string $applicationUrlTemplate)
{
	$url = $applicationUrlTemplate;
	$url = str_replace('{ID}', (string) $event->id, $url); // BC
	$url = str_replace('{id}', (string) $event->id, $url);
	$url = str_replace('{returnUrl}', $event->link, $url);
?>
<?php if ($event->isRegistrationRequired): ?>
	<a
		class="hb-registration__actionButton hb-action hb-action--primary hb-action--lg"
		href="<?php echo $url ?>"
		rel="noopener"
	>
		Chci jet
	</a>
<?php else: ?>
	<p class="hb-registration__text">
		Na tuto akci není třeba se hlásit, stačí přijít. 😉
	</p>
<?php endif; ?>
<?php } ?>


<main role="main">
	<article class="detail">
		<?php if ($hasBeenUnableToLoad): ?>
			<div class="noResults hb-mbe-7">
				Promiňte, zrovna nám vypadl systém, kde máme uloženy všechny informace o plánovaných akcích.
				Zkuste to prosím za chvilku znovu.
			</div>
		<?php else: ?>
			<h1 class="hb-ta-c hb-fvc-sc hb-mbne-3">
				<?php echo $event->title ?>
			</h1>

            <?php if ($event->tags): ?>
			<div class="hb-d-f hb-jc-c">
                <?php hb_tags($event->tags, 'detail__tag') ?>
			</div>
            <?php endif; ?>

			<div class="detail__top hb-mbs-4 hb-d-f hb-lg-ai-c hb-br hb-bg-tinge hb-mbe-5">
				<div class="detail__labels-wrapper">
					<?php if ($event->hasCoverPhoto): ?>
						<a class="detail__coverImage-wrapper" href="<?php echo $event->coverPhotoPath ?>">
							<img class="detail__coverImage" src="<?php if ($event->hasCoverPhoto): echo $event->coverPhotoPath; else: ?>https://brontosaurus.cz/wp-content/uploads/2024/12/logo-hb-brontosaurus.svg<?php endif; ?>" alt="">
						</a>
					<?php else: ?>
						<div class="detail__coverImage detail__coverImage--none"></div>
					<?php endif; ?>

					<div class="detail__labels hb-pi-3 hb-pbe-3 hb-ta-c">
                        <?php hb_eventLabels($event->labels) ?>
					</div>
				</div>

				<div class="detail__basicInformation-wrapper">
					<table class="detail__basicInformation hb-mi-auto hb-mb-4">
						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Datum
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<time datetime="<?php echo $event->dateStartForRobots ?>">
									<?php echo $event->dateSpan ?>
								</time>
							</td>
						</tr>

						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Místo
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<?php if ($event->place->areCoordinatesListed): ?><a class="detail__basicInformation-mapLink" href="https://mapy.cz/zakladni?q=<?php echo $event->place->coordinates ?>" rel="noopener noreferrer" target="_blank"><?php endif; ?>
									<?php echo $event->place->name ?>
                                <?php if ($event->place->areCoordinatesListed): ?></a><?php endif; ?>
							</td>
						</tr>

                        <?php if ($event->age->isListed): ?>
						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Věk
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<?php if ($event->age->isInterval): ?>
									<?php echo $event->age->from ?>–<?php echo $event->age->until ?> let
								<?php elseif ($event->age->isFromListed): ?>
									od <?php echo $event->age->from ?>
									<?php if ($event->age->from === 1): ?>roku<?php else: ?>let<?php endif; ?>
								<?php elseif ($event->age->isUntilListed): ?>
									do <?php echo $event->age->until ?> let
								<?php endif; ?>
							</td>
						</tr>
                        <?php endif; ?>

                        <?php if ($event->hasTimeStart): ?>
						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Začátek akce
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<time>
									<?php echo $event->timeStart ?>
								</time>
							</td>
						</tr>
                        <?php endif; ?>

						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Cena
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<?php if ($event->isPaid): ?>
									<?php echo $event->price ?>
								<?php else: ?>
									<span class="hb-fst-i">bez poplatku</span>
								<?php endif; ?>
							</td>
						</tr>

						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Kontaktní osoba
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<?php if ($event->contact->isPersonListed): ?><?php echo $event->contact->person ?><br><?php endif; ?>
								<?php if ($event->contact->isPhoneListed): ?><a class="detail__basicInformation-contact" href="tel:<?php echo $event->contact->phone ?>" rel="noopener noreferrer" target="_blank"><?php echo hb_formatPhoneNumber($event->contact->phone) ?></a><br><?php endif; ?>
								<a class="detail__basicInformation-contact" href="mailto:<?php echo $event->contact->email ?>" rel="noopener noreferrer" target="_blank"><?php echo $event->contact->email ?></a>
							</td>
						</tr>

                        <?php if ($event->hasRelatedWebsite): ?>
						<tr class="detail__basicInformation-item">
							<th class="detail__basicInformation-label hb-pis-4 hb-pie-4 hb-pbe-2 hb-fw-b hb-ta-r">
								Web akce:
							</th>

							<td class="detail__basicInformation-value hb-pie-4">
								<a class="detail__website" href="<?php echo $event->relatedWebsite ?>" rel="noopener noreferrer" target="_blank">
									<?php echo hb_renderWebsiteUserFriendly($event->relatedWebsite) ?>
								</a>
							</td>
						</tr>
                        <?php endif; ?>
					</table>

                    <?php if ($event->isForFirstTimeAttendees): ?>
					<div class="detail__firstTimeAttendeesInformation hb-pbs-4 hb-pi-4 hb-ta-c">
						Akce je vhodná i pro ty, co s námi
						<a href="/jedu-poprve">jedou poprvé</a>.
					</div>
                    <?php endif; ?>
				</div>
			</div>

			<div class="hb-mbe-5">
				<div class="hb-registration">
					<?php if ($event->isPast): ?>
						<p class="hb-message">
							<strong>Tato akce již proběhla.</strong><br>
							Buď se vrať se zpět, nebo se podívej <a href="/co-se-chysta">co se chystá</a>.
						</p>

					<?php elseif ($event->isFull): ?>
						<p class="hb-message">
							<strong>Tato akce už má bohužel plno.</strong><br>
							Buď se vrať se zpět, nebo se podívej <a href="/co-se-chysta">co se chystá</a>.
						</p>

					<?php else: ?>
                        <?php hb_detail_application($event, $hb_applicationUrlTemplate) ?>
					<?php endif; ?>
				</div>
			</div>

			<!--section 2-->
            <?php if ($event->invitation->introduction): ?>
			<div class="hb-mw-55ch hb-mi-auto hb-mbe-4">
				<h2>
					Co na nás čeká
				</h2>

				<p>
					<?php echo $event->invitation->introduction ?>
				</p>
			</div>
            <?php endif; ?>

			<!--section 3-->
            <?php if ($event->invitation->organizationalInformation): ?>
			<div class="hb-mw-55ch hb-mi-auto hb-mbe-4">
				<h2>
					Co, kde a jak
				</h2>

				<p class="hb-mbe-3">
					<?php echo $event->invitation->organizationalInformation ?>
				</p>

                <?php if ($event->invitation->isAccommodationListed || $event->invitation->isFoodListed): ?>
				<ul class="hb-lst-n">
                    <?php if ($event->invitation->isAccommodationListed): ?>
					<li>Ubytování: <?php echo $event->invitation->accommodation ?></li>
                    <?php endif; ?>

                    <?php if ($event->invitation->isFoodListed): ?>
					<li>
						Strava:
                        <?php echo implode(', ', $event->invitation->food) ?>
					</li>
                    <?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
            <?php endif; ?>

			<!--section 4-->
            <?php if ($event->invitation->isWorkDescriptionListed): ?>
			<div class="hb-mw-55ch hb-mi-auto hb-mbe-4">
				<h2>
					Dobrovolnická pomoc
				</h2>

				<p class="hb-mbe-3">
					<?php echo $event->invitation->workDescription ?>
				</p>

                <?php
				$areDays = $event->invitation->areWorkDaysListed;
				$areHours = $event->invitation->areWorkHoursPerDayListed;
                if ($areDays || $areHours):
                ?>
				<div>
					Na akci budeme pracovat
					<?php if ($areDays): ?>
						<?php echo hb_formatDayCount($event->invitation->workDays) ?><?php if ($areHours): ?>, každý den zhruba <?php echo hb_formatHourCount($event->invitation->workHoursPerDay) ?><?php endif; ?>.
					<?php else: ?>
						přibližně <?php echo hb_formatHourCount($event->invitation->workHoursPerDay) ?> denně.
					<?php endif; ?>
				</div>
                <?php endif; ?>
			</div>
            <?php endif; ?>

			<!--section 5-->
            <?php if ($event->invitation->hasPresentation): ?>
			<div class="hb-mbe-4">
				<div class="hb-mw-55ch hb-mi-auto hb-mbe-3">
					<h2>
						Malá ochutnávka
					</h2>

                    <?php if ($event->invitation->presentation->hasText): ?>
					<p>
						<?php echo $event->invitation->presentation->text ?>
					</p>
                    <?php endif; ?>
				</div>

                <?php if ($event->invitation->presentation->hasAnyPhotos): ?>
				<ul class="photogallery">
                    <?php foreach ($event->invitation->presentation->photos as $photo): ?>
					<li class="photogallery__item">
						<a class="photogallery__link" href="<?php echo $photo ?>" data-glightbox data-gallery="gallery">
							<img src="<?php echo $photo ?>" alt="" class="photogallery__image">
						</a>
					</li>
                    <?php endforeach; ?>
				</ul>
                <?php endif; ?>
			</div>
            <?php endif; ?>

			<!--section 5-->
			<div class="hb-mw-55ch hb-mi-auto hb-mbe-4">
				<h2>
					Organizační tým
				</h2>

				<p>
					<?php if ($event->areOrganizersListed): ?>Těší se na tebe <?php echo $event->organizers ?>.<br><?php endif; ?>
					Pořádá <?php echo $event->organizerUnit ?>
				</p>
			</div>

			<!--section 6-->
			<div class="hb-mw-55ch hb-mi-auto hb-mbe-5">
				<h2>
					Kontakt
				</h2>

				<div class="detail__data">
					<?php if ($event->contact->isPersonListed): ?>Kontaktní osoba: <?php echo $event->contact->person ?><br><?php endif; ?>
					E-mail: <a href="mailto:<?php echo $event->contact->email ?>" rel="noopener noreferrer" target="_blank"><?php echo $event->contact->email ?></a><br>
					<?php if ($event->contact->isPhoneListed): ?>Telefon: <a href="tel:<?php echo $event->contact->phone ?>" rel="noopener noreferrer" target="_blank"><?php echo hb_formatPhoneNumber($event->contact->phone) ?></a><?php endif; ?>

					<?php if ($event->hasRelatedWebsite): ?>
						<br>
						Webová stránka: <a class="detail__website" href="<?php echo $event->relatedWebsite ?>" rel="noopener noreferrer" target="_blank">odkaz</a>
					<?php endif; ?>
				</div>
			</div>

            <?php if ( ! $event->isPast && ! $event->isFull): ?>
			<div class="hb-mbe-6">
				<div class="hb-registration">
                    <?php hb_detail_application($event, $hb_applicationUrlTemplate) ?>
				</div>
			</div>
            <?php endif; ?>
		<?php endif; ?>
	</article>
</main>

<aside>
	<section class="hb-mi-auto hb-mw-60ch">
		<h1>
			Proč jet s Brontosaurem
		</h1>

		<p class="hb-mbe-3">
			Za více než 50 let své existence se brontosaurus naučil propojovat dobrovolnictví, zážitky a neformální vzdělávání
			tak, aby vznikaly akce, které pomohou nejen přírodní lokalitě nebo památkám, ale i jejich účastníkům.
			Potkej se s partou lidí, kteří se zajímají o svět kolem sebe a kteří, stejně jako ty, hledají možnost se rozvíjet
			a poznat nové přátele.
		</p>

		<p class="hb-mbe-3">
			Dobrovolnictví je pro brontosaura cestou k novým zážitkům, přátelům a dovednostem. Díky našim akcím se
			podařilo nejen opravit množství památek, ochraňovat jedinečné živočišné i rostlinné druhy nebo navázat
			na české kořeny na Balkáně, ale i naučit spoustu lidí spoustu věcí, získat přátelství na celý život a ukázat
			každému úplně nový svět.
		</p>

		<a class="hb-mbs-4 hb-action hb-action--ulterior" href="/o-brontosaurovi">více o Brontosaurovi</a>
	</section>
</aside>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const flashMessageElement = document.querySelector('.detail__flashMessage');
    if (flashMessageElement === null) {
        return;
    }

    let closeButtonElement = flashMessageElement.querySelector('.detail__flashMessage-closeButton');
    if (closeButtonElement === null) {
        return;
    }

    closeButtonElement.addEventListener('click', () => 
        flashMessageElement.classList.add('detail__flashMessage--hidden'));
});
</script>