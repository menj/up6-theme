<?php
/**
 * Template Name: The Meaning of 6
 * Template Post Type: page
 *
 * Maksud 6 — the editorial identity and founding principles of UP6 Suara Semasa.
 * File naming: en-US per UP6 convention.
 * Content strings: en-US source, translated via ms_MY.po.
 *
 * @package UP6
 */

get_header();
?>

<div class="site-content-inner">
  <?php up6_breadcrumb(); ?>

  <main id="main" class="site-main policy-main" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      <header class="policy-header">
        <p class="policy-header-label">
          <span class="section-dot" aria-hidden="true"></span>
          <?php esc_html_e( 'Our Identity', 'up6' ); ?>
        </p>
        <h1 class="entry-title"><?php the_title(); ?></h1>
      </header>

      <?php ob_start(); ?>
      <div class="policy-content meaning-of-6">

        <?php /* ── Cold open ── */ ?>
        <p><?php esc_html_e( 'Most news gives you one version. One angle. One approved reading of events, delivered with the confidence of institutions that have never had to answer for being wrong.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'UP6 was built on a different premise: that good journalism is not a single voice amplified, but multiple lenses applied — with rigour, in your language, without permission from power.', 'up6' ); ?></p>
        <p class="meaning-claim"><?php esc_html_e( 'The 6 in UP6 is not incidental. It is a claim.', 'up6' ); ?></p>

        <?php /* ── The Sixth Estate ── */ ?>
        <h2><?php esc_html_e( 'The Sixth Estate', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The press has long been called the Fourth Estate — the informal check on the three formal branches of government. The Fifth Estate emerged with the internet: bloggers, citizen journalists, social networks that broke the monopoly of the masthead and gave the unaccredited a platform.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'The Sixth Estate is what comes after the noise. It is journalism that is community-grounded rather than commercially captured. Language-first rather than language-last. Accountable to its readers rather than to its owners, its advertisers, or the government of the day. It does not wait for a press accreditation to tell the truth. It does not moderate its conclusions to preserve access to those in power.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'UP6 Suara Semasa claims that ground.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We are not neutral — neutrality is a convenient fiction used to avoid accountability. We are independent. We operate in Bahasa Melayu because that is where the conversation that matters to us lives, and because language is never merely a vehicle: it is a political choice. We answer to our readers. We are the Sixth Estate.', 'up6' ); ?></p>
        <p>
          <?php
          printf(
            /* translators: %s: "Akta Sakit Hati 1998" in italics */
            esc_html__( 'We are also aware that independent journalism in Malaysia operates under the shadow of laws designed less to protect the public than to protect power from scrutiny — among them Section 233 of the Communications and Multimedia Act 1998, known in practice, and with good reason, as the %s. We publish anyway. A law that exists to punish inconvenient expression is not a law that deserves our silence. It is a law that deserves to be named.', 'up6' ),
            '<em>' . esc_html__( 'Akta Sakit Hati 1998', 'up6' ) . '</em>'
          );
          ?>
        </p>

        <?php /* ── Six Voices ── */ ?>
        <h2><?php esc_html_e( 'Six Voices', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'Malaysian public discourse has been systematically narrowed. Media ownership is concentrated in the hands of entities with direct or indirect ties to political power. Access to print and broadcast licences has historically been conditioned on political alignment. Language hierarchy has long treated Bahasa Melayu as a domestic concern and English as the language of record, producing a media landscape in which the majority language is the minority voice. The result is a press that, with honourable exceptions, serves the powerful better than it serves the public.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'UP6 exists in opposition to that narrowing. Not through confrontation for its own sake — confrontation without journalism is just noise — but through the sustained, unglamorous act of publishing: consistently, independently, in the language of the majority, what the concentrated press would rather leave unreported.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'The six voices are not six named individuals. They are the six communities whose concerns too often find no reflection in the national media:', 'up6' ); ?></p>

        <div class="meaning-voices">
          <div class="meaning-voice" data-voice="rural">
            <h3><?php esc_html_e( 'The Rural Reader', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'Who opens the paper and does not see their reality — the longhouse with no clean water, the padi farmer whose subsidy was quietly restructured, the fishing community whose sea is being reclaimed — because it does not make for comfortable reading in a lifestyle supplement.', 'up6' ); ?></p>
          </div>
          <div class="meaning-voice" data-voice="urban">
            <h3><?php esc_html_e( 'The Urban Working Class', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'Whose economic precarity is abstracted into headline GDP figures and investment corridor announcements, while the actual experience of not being able to afford rent in the city where you work goes unreported because it implies a question no one in power wants asked.', 'up6' ); ?></p>
          </div>
          <div class="meaning-voice" data-voice="youth">
            <h3><?php esc_html_e( 'The Young Malaysian', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'Whose political consciousness is dismissed as inexperience, whose anger at inherited dysfunction is reframed as ingratitude, and whose engagement with public life is simultaneously demanded and punished when it produces inconvenient conclusions.', 'up6' ); ?></p>
          </div>
          <div class="meaning-voice" data-voice="minority">
            <h3><?php esc_html_e( 'The Minority', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'Linguistic, ethnic, religious — whose language and culture are treated as footnotes to a national narrative written without them, and whose concerns are addressed, when they are addressed at all, as special pleading rather than as the legitimate claims of citizens.', 'up6' ); ?></p>
          </div>
          <div class="meaning-voice" data-voice="dissent">
            <h3><?php esc_html_e( 'The Dissenter', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'The activist, the whistleblower, the academic whose research produces uncomfortable findings, the journalist who published the story that required courage — whose criticism of power is framed as disloyalty, whose motives are questioned before their evidence is examined, and who discovers that in Malaysia, what you say matters less than who it inconveniences.', 'up6' ); ?></p>
          </div>
          <div class="meaning-voice" data-voice="ordinary">
            <h3><?php esc_html_e( 'The Ordinary Person', 'up6' ); ?></h3>
            <p><?php esc_html_e( 'Who wants, without ideology or agenda, to know what is actually true — what the government actually decided, what the court actually found, what the money was actually spent on — and who deserves journalism that respects that want enough to answer it honestly.', 'up6' ); ?></p>
          </div>
        </div>

        <p class="meaning-built-for"><?php esc_html_e( 'UP6 Suara Semasa is built for all six.', 'up6' ); ?></p>

        <?php /* ── Six Lenses ── */ ?>
        <h2><?php esc_html_e( 'Six Lenses', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'UP6 does not just cover stories. It frames them — through six distinct angles that together constitute a complete picture of the world as it affects Malaysians. Every story that arrives in our newsroom is interrogated through at least one of these lenses. Most require several.', 'up6' ); ?></p>

        <dl class="meaning-lenses">
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Domestik', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'What happens here. The decisions made in Putrajaya, the verdicts handed down in our courts, the policies that shape ordinary life. Domestic coverage is the core of what we do and the hardest to do honestly, because the people it scrutinises are the same people with the power to make a journalist\'s life difficult. We cover it anyway.', 'up6' ); ?></dd>
          </div>
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Serantau', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'What connects us to the region. Malaysia does not exist in isolation from ASEAN, from the South China Sea, from the shifting geometries of regional power and the quiet rearrangements of influence that never make the front page. Serantau coverage traces those connections — the ones that matter before anyone admits they matter.', 'up6' ); ?></dd>
          </div>
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Global', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'What reaches us from beyond. Geopolitics, conflict, climate, international markets — the world arrives on our doorstep whether we report it or not. We report it, and we report it through Malaysian eyes: not as spectators to history but as people with a stake in how it unfolds.', 'up6' ); ?></dd>
          </div>
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Ekonomi', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'What moves money and lives. From Bank Negara policy to ringgit volatility to the informal economy that most financial journalism ignores because its subjects do not read Bloomberg. Economic coverage at UP6 is written for readers, not for traders. The question we ask is not what this means for the market. It is what this means for you.', 'up6' ); ?></dd>
          </div>
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Budaya', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'What makes us who we are. Language, literature, faith, identity, memory. Budaya coverage is where UP6\'s commitment to Bahasa Melayu is most visible — not as a concession to audience but as a conviction about what matters and what is worth preserving. We cover culture as seriously as we cover politics, because culture is politics by other means.', 'up6' ); ?></dd>
          </div>
          <div class="meaning-lens">
            <dt><?php esc_html_e( 'Manusia', 'up6' ); ?></dt>
            <dd><?php esc_html_e( 'The human behind every story. The person the statistic describes. The community the policy erases. The name that becomes a number in a government report. Manusia coverage is the reminder that journalism is ultimately about people — not events, not institutions, not quarterly figures — and that the measure of a report is whether the person it describes would recognise themselves in it.', 'up6' ); ?></dd>
          </div>
        </dl>

        <?php /* ── Six Commitments ── */ ?>
        <h2><?php esc_html_e( 'Six Commitments', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The 6 is also a promise. These are the six things UP6 Suara Semasa commits to on every story, on every day, without exception and without asterisks.', 'up6' ); ?></p>

        <dl class="meaning-commitments">
          <div class="meaning-commitment" data-commit="ketepatan">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Ketepatan', 'up6' ); ?></span>
              <div class="commit-body"><?php esc_html_e( 'We verify before we publish. We do not treat speed as an excuse for error, or traffic as a justification for speculation. Every claim that carries our name has been interrogated before it is printed — because our word to the reader is the only currency we have, and we do not spend it carelessly.', 'up6' ); ?></div>
            </dd>
          </div>
          <div class="meaning-commitment" data-commit="kebebasan">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Kebebasan', 'up6' ); ?></span>
              <div class="commit-body"><?php esc_html_e( 'Our editorial decisions are made by our editorial team. Not by our advertisers. Not by our shareholders. Not by any government body, political party, regulatory authority or law enforcement agency. The moment an outside party begins directing what we publish or suppress, we are no longer a news organisation. We are a mouthpiece. We refuse that role.', 'up6' ); ?></div>
            </dd>
          </div>
          <div class="meaning-commitment" data-commit="keadilan">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Keadilan', 'up6' ); ?></span>
              <div class="commit-body"><?php esc_html_e( 'We give the subjects of our reporting the right of reply. We present competing accounts where they exist and clearly label them as such. We distinguish between what is established and what is alleged. We do not use journalism as a weapon against individuals, and we do not use it as a shield for institutions. Fairness is a method, not a conclusion — and it is never an excuse for cowardice.', 'up6' ); ?></div>
            </dd>
          </div>
          <div class="meaning-commitment" data-commit="ketelusan">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Ketelusan', 'up6' ); ?></span>
              <div class="commit-body"><?php esc_html_e( 'We name our sources where we can. Where we cannot, we explain why — not with a vague appeal to confidentiality, but with a specific account of the editorial judgement made. We do not hide our methods, our funding, our ownership or our limitations. Readers who disagree with our judgements are entitled to the information needed to challenge them.', 'up6' ); ?></div>
            </dd>
          </div>
          <div class="meaning-commitment meaning-commitment--keberanian" data-commit="keberanian">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Keberanian di Hadapan Kuasa', 'up6' ); ?></span>
              <div class="commit-body">
                <p><?php esc_html_e( 'Power does not like being watched. Uniformed power likes it least of all.', 'up6' ); ?></p>
                <p><?php esc_html_e( 'UP6 Suara Semasa will report on the conduct of the police, the judiciary, the military and the apparatus of the state with the same scrutiny we apply to any other institution — without deference, without prior consultation, and without adjusting our conclusions to suit those who carry badges or batons. The uniform is not a shield from accountability. It is, if anything, a reason to look harder.', 'up6' ); ?></p>
                <p><?php esc_html_e( 'We will name custodial deaths. We will report unlawful detention, the abuse of remand powers, and the use of force against civilians as the serious matters they are — not as isolated incidents, not as procedural anomalies, but as events that demand explanation from those responsible. We will not deploy the word alleged to protect institutions that have the full resources of the state at their disposal. We will not kill a story because someone in authority found it inconvenient.', 'up6' ); ?></p>
                <p><?php esc_html_e( 'We do not accept the premise that a free press owes the state the benefit of the doubt. The state has lawyers, spokespersons, press secretaries and the full apparatus of official narrative. It does not need our help. The public, which has none of those things, does.', 'up6' ); ?></p>
                <p>
                  <?php
                  printf(
                    /* translators: %s: link to Polis Raja di Malaysia */
                    esc_html__( 'The structural relationship between the Malaysian police force and political power in this country has been examined with rigour and without illusion. We commend that examination to our readers: %s — the title alone says what we mean.', 'up6' ),
                    '<a href="https://langgamfikir.my/publications/polis-raja-di-malaysia/" target="_blank" rel="noopener"><em>' . esc_html__( 'Polis Raja di Malaysia', 'up6' ) . '</em></a>'
                  );
                  ?>
                </p>
              </div>
            </dd>
          </div>
          <div class="meaning-commitment" data-commit="pembetulan">
            <dt></dt>
            <dd>
              <span class="commit-name"><?php esc_html_e( 'Pembetulan Tanpa Syarat', 'up6' ); ?></span>
              <div class="commit-body"><?php esc_html_e( 'When we are wrong — and we will be wrong, because journalism is a human enterprise — we say so. At the top of the article. In plain language. With the corrected version clearly stated. No buried footnotes. No quiet edits in the dead of night. No passive-voice non-apologies that acknowledge the error while avoiding the embarrassment. Our credibility is not built by never being wrong. It is built by how we behave when we are.', 'up6' ); ?></div>
          </div>
        </dl>

        <?php /* ── The Position ── */ ?>
        <h2><?php esc_html_e( 'The Position', 'up6' ); ?></h2>
        <p><?php esc_html_e( 'The 6 in UP6 is not a number.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'It is a position — on what journalism is for, on who it serves, on what it owes the people who read it.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'It is a claim to the Sixth Estate: independent, language-first, answerable to no authority but the truth and the public interest.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'It is six lenses through which every story is seen, six commitments we make on every byline, six voices we exist to make audible.', 'up6' ); ?></p>
        <p><?php esc_html_e( 'We did not build UP6 Suara Semasa to be comfortable. We built it to be useful. We built it to be honest. We built it to still be standing — and still publishing — when the pressure comes.', 'up6' ); ?></p>
        <p class="meaning-closing-line"><?php esc_html_e( 'If you are reading this, you are why we exist.', 'up6' ); ?></p>

        <?php /* ── SUARA ── */ ?>
        <div class="meaning-suara">
          <h2>SUARA</h2>
          <p class="meaning-suara-intro"><?php esc_html_e( 'The name was never just a name.', 'up6' ); ?></p>

          <dl class="meaning-suara-acronym">
            <div>
              <dt><span class="suara-letter">S</span><?php esc_html_e( 'Sahih', 'up6' ); ?></dt>
              <dd><?php esc_html_e( 'We verify before we publish. Our word to the reader is the only currency we have, and we do not spend it carelessly.', 'up6' ); ?></dd>
            </div>
            <div>
              <dt><span class="suara-letter">U</span><?php esc_html_e( 'Utuh', 'up6' ); ?></dt>
              <dd><?php esc_html_e( 'Uncompromised. Our editorial decisions belong to us alone — not to our advertisers, not to our owners, not to anyone who carries a badge or a portfolio.', 'up6' ); ?></dd>
            </div>
            <div>
              <dt><span class="suara-letter">A</span><?php esc_html_e( 'Adil', 'up6' ); ?></dt>
              <dd><?php esc_html_e( 'Fair in method. We give the right of reply. We present competing accounts. We do not use journalism as a weapon — but we do not use fairness as an excuse for cowardice either.', 'up6' ); ?></dd>
            </div>
            <div>
              <dt><span class="suara-letter">R</span><?php esc_html_e( 'Rakyat', 'up6' ); ?></dt>
              <dd><?php esc_html_e( 'Built for the public. Answerable to the public. Nobody else.', 'up6' ); ?></dd>
            </div>
            <div>
              <dt><span class="suara-letter">A</span><?php esc_html_e( 'Amanah', 'up6' ); ?></dt>
              <dd><?php esc_html_e( 'Journalism is a trust. The reader gives us their time and their confidence. We hold that as a duty, not a transaction.', 'up6' ); ?></dd>
            </div>
          </dl>

          <p class="meaning-suara-close"><?php esc_html_e( 'SUARA. Voice. It was always the right word.', 'up6' ); ?></p>
        </div>

      </div><!-- .policy-content -->
      <?php echo up6_brand_inline( ob_get_clean() ); ?>

    </article>
    <?php endwhile; ?>

  </main>
</div>

<?php get_footer(); ?>
