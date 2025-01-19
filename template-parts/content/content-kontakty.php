<?php

$posts = get_posts(['post_type' => 'contacts', 'numberposts' => -1]);

/** @var array<stdClass{name: string, hasPhoto: bool, photo: ?string, role: string, about: string, emailAddresses: string[]}> $contacts */
$contacts = array_map(function (WP_Post $post) {
    $thumbnail = get_the_post_thumbnail_url($post);
    $thumbnail = $thumbnail === false ? null : $thumbnail; // convert false to null which makes more sense

    $emailAddresses = explode("\n", get_post_meta($post->ID, 'contacts_email', single: true));
    if ($emailAddresses === false || $emailAddresses[0] === '') {
        $emailAddresses = [];
    }

    return (object) [
        'name' => $post->post_title,
        'hasPhoto' => $thumbnail !== null,
        'photo' => $thumbnail,
        'role' => get_post_meta($post->ID, 'contacts_role', single: true),
        'about' => get_post_meta($post->ID, 'contacts_about', single: true),
        'emailAddresses' => $emailAddresses,
    ];
}, $posts);

?>

<main role="main">
	<h1>
		Kontakty na Hnutí Brontosaurus
	</h1>

	<div class="contacts">
		<div class="hb-block-text hb-mbe-5">
			<p>
				Lidé z ústředí Hnutí Brontosaurus se spolu s dobrovolníky starají o chod organizace
				a zázemí jednotlivých programů. Seznam se s námi a kontaktuj nás.
			</p>

			<p>
				V různých městech také působí: <a href="/o-brontosaurovi/hnuti-brontosaurus#mapa-zakladni-clanky">články</a>, <a href="/o-brontosaurovi/hnuti-brontosaurus#mapa-kluby">kluby</a> a <a href="/o-brontosaurovi/hnuti-brontosaurus#mapa-regionalni-centra">regionální centra</a>.
			</p>
		</div>

		<div class="hb-mbe-5">
			<h2>
				Ústředí Hnutí Brontosaurus
			</h2>

			<div class="contacts__box hb-mi-auto hb-mbe-5 hb-br">
				<iframe class="contacts__map hb-is-100 hb-bs-100" src="https://frame.mapy.cz/s/karunuveku" style="border:none" frameborder="0"></iframe>

				<address class="contacts__boxInner hb-p-5 hb-fs-s hb-fst-n hb-bg-tinge">
					<div class="contacts__boxItem contacts__boxItem--address hb-mbe-3">
						<h3 class="screenreaders-only" id="contacts-address">
							Kontaktní adresa
						</h3>

						<a href="https://mapy.cz/s/kavuvehele" target="_blank" aria-labelledby="contacts-address">
							Cejl 35, Brno, 602 00
						</a>
					</div>

					<div class="contacts__boxItem contacts__boxItem--phone hb-mbe-3">
						<h3 class="screenreaders-only" id="contacts-phone">
							Telefon
						</h3>

						<div aria-labelledby="contacts-phone">
							<a class="hb-d-b" href="tel:+420734392735" target="_blank">+420 734 392 735</a>
						</div>

						<div class="hb-fs-xxs">
							mimo úřední hodiny:
							<ul class="hb-mis-3">
								<li><a href="tel:+420737034230" target="_blank">+420 737 034 230</a> – finance, projekty</li>
								<li><a href="tel:+420605763112" target="_blank">+420 605 763 112</a> – média, partneři, spolupráce a ostatní</li>
							</ul>
						</div>
					</div>

					<div class="contacts__boxItem contacts__boxItem--email hb-mbe-3">
						<h3 class="screenreaders-only" id="contacts-email">
							E&ndash;mail
						</h3>

						<a href="mailto:hnuti@brontosaurus.cz" target="_blank" aria-labelledby="contacts-email">
							hnuti@brontosaurus.cz
						</a>
						<div class="hb-fs-xxs">obecný informační mail</div>
						<a href="mailto:dalimil.toman@brontosaurus.cz" target="_blank">
							dalimil.toman@brontosaurus.cz
						</a>
						<div class="hb-fs-xxs">média, partneři, spolupráce</div>
					</div>
				</address>

				<div class="contacts__openingHours [ hb-pbs-4 hb-pi-5 hb-pbe-5 ] hb-bg-tinge">
					<h3>
						Kdy jsme v kanceláři

						<div class="hb-fs-xs">(úřední hodiny)</div>
					</h3>

					<div class="contacts__openingDays">
						<span class="hb-fw-b">Aktuálně stěhujeme</span> naši kancelář na novou lokalitu, její adresa a otevírací hodiny budou brzy zveřejněny.
						V případě potřeby osobního jednání nás <span class="hb-fw-b">kontaktujte na uvedených telefonech</span>.
					</div>
				</div>
			</div>

			<div class="hb-block-text hb-fs-s hb-mbe-4">
				<strong>Kontrolní a revizní komise HB</strong><br>
				<span class="hb-fs-xs">kontakt pro podněty, připomínky či případné stížnosti k akcím nebo činnosti HB a jeho pobočných spolků</span><br>
				<a class="contacts__otherLink hb-fs-xs" href="mailto:krk@brontosaurus.cz" rel="noopener">krk@brontosaurus.cz</a>
			</div>

			<div class="hb-block-text hb-fs-s hb-mbe-4">
				<strong>Výkonný výbor HB</strong><br>
				<span class="hb-fs-xs">kontakt pro náměty a dotazy pro volený řídící orgán</span><br>
				<a class="contacts__otherLink hb-fs-xs" href="mailto:vv@brontosaurus.cz" rel="noopener">vv@brontosaurus.cz</a>
			</div>

			<div class="hb-block-text hb-fs-s hb-mbe-5">
				<strong>Předsedkyně HB – Alena Konečná</strong><br>
				<a class="contacts__otherLink hb-fs-xs" href="mailto:predsedkyne@brontosaurus.cz" rel="noopener">predsedkyne@brontosaurus.cz</a>
			</div>

			<div class="hb-block-text hb-fs-xs">
				IČ: 0040 8328<br>
				Zápis je provedený v obchodním registru
				pod sp. zn. L 346 vedený u Krajského soudu v Brně.
			</div>

			<div class="hb-block-text hb-fs-xs">
				ID datové schránky: h7id376<br>
				transparentní účet: <a href="https://ib.fio.cz/ib/transparent?a=2100070590" rel="noopener noreferrer" target="_blank">2100070590/2010</a><br>
				transparentní účet pro příjem darů – Adopce brontosaura: <a href="https://ib.fio.cz/ib/transparent?a=2600217667" rel="noopener noreferrer" target="_blank">2600217667/2010</a>
			</div>
		</div>

		<div>
			<h2>
				Naši lidé
			</h2>

            <?php foreach ($contacts as $contact): ?>
			<div class="[ hb-lg-d-f hb-lg-fd-r hb-lg-jc-sb ] [ hb-sm-c-r ] hb-mw-40 hb-mi-auto hb-mbe-5">
				<div class="contacts__imageWrapper hb-sm-f-r">
                    <?php if ($contact->hasPhoto): ?>
					<img class="contacts__image hb-mw-full hb-mbe-3 hb-lg-mbe-0" src="<?php echo $contact->photo ?>" alt="">
                    <?php endif; ?>
				</div>

				<div class="contacts__description hb-lg-d-f hb-lg-fd-c hb-lg-jc-c">
					<div class="hb-mbe-3">
						<span class="hb-fw-b"><?php echo $contact->name ?></span>
						<br>
						<?php echo $contact->role ?>
					</div>

					<div class="contacts__about hb-mbe-3">
						<?php echo $contact->about ?>
					</div>

					<?php if (count($contact->emailAddresses) > 0): ?>
					<div class="contacts__mail hb-pis-4">
                        <?php foreach ($contact->emailAddresses as $index => $emailAddress): ?>
                        <?php if ($index > 0): ?><br><?php endif; ?>
						<a href="mailto:<?php echo $emailAddress ?>" target="_blank"><?php echo $emailAddress ?></a>
                        <?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
            <?php endforeach; ?>
		</div>
	</div>
</main>
