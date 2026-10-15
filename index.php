<?php
declare(strict_types=1);
session_start();

$fallbackProjects = [
    [
        'title' => 'KankuDiamond - WooCommerce & Supplier API',
        'category' => 'Ecommerce & Integrations',
        'summary' => 'Diamond ecommerce platform with automated supplier REST API synchronization of 700-800 products daily, custom attributes, certificate viewing, and 360-degree product previews.',
        'technologies' => 'WordPress, WooCommerce, PHP, REST APIs, MySQL, JavaScript',
        'details' => 'Integrated external supplier REST API to retrieve, map, and synchronize diamond product data with an automated daily sync of 700-800 items. Customized WooCommerce product attributes, diamond specs, grading certificate display, and 360-degree interactive product preview. Built customized customer registration/login flows with extra business communication fields, and resolved supplier API large-payload bottlenecks through robust batch processing.',
        'featured' => 1,
    ],
    [
        'title' => 'Geminismart / Max ERP - Estimates Module',
        'category' => 'Enterprise ERP Application',
        'summary' => 'Estimates Management Module for an enterprise ERP platform, featuring a multi-step estimate creation wizard, server-side DataTables, price lists, CSV import parser, and attachments.',
        'technologies' => 'PHP, CodeIgniter, MySQL, JavaScript, AJAX, DataTables, REST APIs, CSV',
        'details' => 'Contributed comprehensive frontend and backend functionality to the Estimates Module. Implemented high-performance server-side DataTables for estimate listings, built REST API endpoints for estimates and price list management, developed a flexible CSV import parser (supporting item codes, quantities, unit prices, discounts, and VAT), and engineered secure estimate attachment handling.',
        'featured' => 1,
    ],
    [
        'title' => 'Belt Stars - Custom WooCommerce Store',
        'category' => 'Ecommerce / Custom Workflows',
        'summary' => 'Full-scale custom WooCommerce online store built from scratch with payment gateway integrations, USPS & Ship and Drop shipping, subscriptions, and wholesale pricing.',
        'technologies' => 'WordPress, WooCommerce, PHP, JavaScript, CSS, REST APIs',
        'details' => 'Developed a custom WooCommerce store to exact client specifications, configuring product catalogs, secure checkout, and payment gateways. Integrated third-party shipping APIs (Ship and Drop, USPS), implemented recurring subscription management, created a custom support ticketing workflow, and configured wholesale tiered pricing.',
        'featured' => 1,
    ],
    [
        'title' => 'Strong & Stable Gym - LMS & Fitness Commerce',
        'category' => 'EdTech & Ecommerce Platform',
        'summary' => 'Integrated fitness portal combining LearnDash LMS course delivery with WooCommerce monetization for selling online fitness workout programs.',
        'technologies' => 'WordPress, WooCommerce, LearnDash LMS, PHP, JavaScript, CSS',
        'details' => 'Engineered an integrated fitness and e-learning platform connecting LearnDash LMS with WooCommerce. Built seamless automated course enrollment on checkout completion, customized plugin hooks and filters for subscription-based workout content, and designed responsive mobile-first layouts.',
        'featured' => 1,
    ],
];

@include_once __DIR__ . '/config/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = '';
$error = '';
$name = '';
$email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // Honeypot spam check
    if (!empty($_POST['website'])) {
        $success = 'Thank you for your message!';
    } else {
        $token = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            $error = 'Your session expired. Please refresh the page and try again.';
        } else {
            $name = trim((string)($_POST['name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $subject = trim((string)($_POST['subject'] ?? ''));
            $message = trim((string)($_POST['message'] ?? ''));

            if ($name === '' || $email === '' || $message === '') {
                $error = 'Please complete your name, email, and message.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } elseif (strlen($message) > 5000) {
                $error = 'Please keep your message under 5,000 characters.';
            } else {
                if (isset($pdo) && $pdo instanceof PDO) {
                    try {
                        $stmt = $pdo->prepare(
                            'INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)'
                        );
                        $stmt->execute([
                            ':name' => $name,
                            ':email' => $email,
                            ':subject' => $subject,
                            ':message' => $message,
                        ]);
                        $success = 'Thanks for reaching out! Your message has been sent successfully.';
                        $name = $email = $subject = $message = '';
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    } catch (PDOException $e) {
                        $error = 'The database is currently busy. Please contact me directly via email or WhatsApp.';
                    }
                } else {
                    $success = 'Thank you! Your message was received.';
                    $name = $email = $subject = $message = '';
                }
            }
        }
    }
}

$projects = [];
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $projects = $pdo->query('SELECT title, category, summary, technologies, details, featured FROM projects ORDER BY featured DESC, id ASC')->fetchAll();
    } catch (PDOException $e) {
        $projects = $fallbackProjects;
    }
}
if (empty($projects)) {
    $projects = $fallbackProjects;
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dipen Modi — PHP Backend Developer and WordPress/WooCommerce Developer with 3+ years of experience building e-commerce platforms and web applications.">
    <meta property="og:title" content="Dipen Modi | PHP Backend & WordPress Developer">
    <meta property="og:description" content="3+ years building e-commerce platforms, custom plugins, database-driven applications, and REST API integrations for international clients.">
    <meta property="og:type" content="website">
    <title>Dipen Modi | PHP Backend & WordPress Developer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="#home" aria-label="Dipen Modi home"><span class="brand-mark">DM</span><span>Dipen Modi<span class="brand-dot">.</span></span></a>
        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <nav class="nav-links" aria-label="Main navigation">
            <a href="#about">About</a><a href="#skills">Skills</a><a href="#projects">Projects</a><a href="#experience">Experience</a><a href="#education">Education</a><a class="nav-cta" href="#contact">Let's talk <span>↗</span></a>
        </nav>
    </div>
</header>

<main>
<section class="hero section" id="home">
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="eyebrow"><span class="status-dot"></span> OPEN TO NEW OPPORTUNITIES · 3+ YEARS EXP</div>
            <p class="hero-kicker">PHP BACKEND · WORDPRESS · WOOCOMMERCE</p>
            <h1>I build scalable web experiences <span>that work under the hood.</span></h1>
            <p class="hero-description">I'm Dipen Modi, a PHP Backend and WordPress/WooCommerce Developer with 3+ years of experience building e-commerce platforms, custom plugins, database-driven applications, and REST API integrations for international clients across the US, UK, and European markets.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#projects">Explore my work <span>↘</span></a>
                <a class="button button-secondary" href="#contact">Contact me</a>
                <a class="button button-secondary" href="https://wa.me/918200217135" target="_blank" rel="noopener noreferrer">WhatsApp <span>↗</span></a>
            </div>
            <div class="hero-meta">
                <span><i class="meta-line"></i> Surat, Gujarat, India</span>
                <span>WordPress Developer · Narola Infotech</span>
            </div>
        </div>
        <div class="hero-visual" aria-label="Decorative code card">
            <div class="visual-glow"></div>
            <div class="code-window">
                <div class="window-top"><div class="window-dots"><b></b><b></b><b></b></div><span>Developer.php</span><span class="window-lock">⌘</span></div>
                <div class="code-body">
                    <div><span class="line-no">01</span><span class="code-purple">&lt;?php</span></div>
                    <div><span class="line-no">02</span><span class="code-pink">class</span> <span class="code-green">DipenModi</span> <span class="code-pink">extends</span> <span class="code-green">BackendEngineer</span></div>
                    <div><span class="line-no">03</span>{</div>
                    <div><span class="line-no">04</span>&nbsp;&nbsp;<span class="code-blue">public int</span> <span class="code-white">$experience</span> = <span class="code-yellow">3</span>; <span class="code-purple">// Years</span></div>
                    <div><span class="line-no">05</span>&nbsp;&nbsp;<span class="code-blue">public array</span> <span class="code-white">$coreStack</span> = [</div>
                    <div><span class="line-no">06</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-yellow">'PHP'</span>, <span class="code-yellow">'CodeIgniter'</span>, <span class="code-yellow">'MySQL'</span>,</div>
                    <div><span class="line-no">07</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-yellow">'WordPress'</span>, <span class="code-yellow">'WooCommerce'</span>, <span class="code-yellow">'REST APIs'</span></div>
                    <div><span class="line-no">08</span>&nbsp;&nbsp;];</div>
                    <div><span class="line-no">09</span>&nbsp;&nbsp;<span class="code-pink">public function</span> <span class="code-green">deliverSolution</span>(): <span class="code-blue">array</span></div>
                    <div><span class="line-no">10</span>&nbsp;&nbsp;{</div>
                    <div><span class="line-no">11</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-pink">return</span> [</div>
                    <div><span class="line-no">12</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-yellow">'performance'</span> => <span class="code-green">true</span>,</div>
                    <div><span class="line-no">13</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-yellow">'clean_architecture'</span> => <span class="code-green">true</span>,</div>
                    <div><span class="line-no">14</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="code-yellow">'client_satisfaction'</span> => <span class="code-green">true</span></div>
                    <div><span class="line-no">15</span>&nbsp;&nbsp;&nbsp;&nbsp;];</div>
                    <div><span class="line-no">16</span>&nbsp;&nbsp;}</div>
                    <div><span class="line-no">17</span>}</div>
                </div>
                <div class="code-footer"><span><i></i> Star Performer & Emerging Star</span><span>PHP 7.4 / 8.x</span></div>
            </div>
            <div class="floating-card floating-card-top"><span class="floating-icon">⌘</span><div><strong>Backend & APIs</strong><small>CodeIgniter · MySQL · REST</small></div></div>
            <div class="floating-card floating-card-bottom"><span class="floating-icon green-icon">↗</span><div><strong>3+ Years Experience</strong><small>10+ Websites Delivered</small></div></div>
        </div>
    </div>
    <div class="container tech-strip">
        <span>CORE STACK</span>
        <div>PHP</div>
        <div>CODEIGNITER</div>
        <div>WORDPRESS</div>
        <div>WOOCOMMERCE</div>
        <div>MYSQL</div>
        <div>REST APIs</div>
        <div>DATATABLES</div>
        <div>JAVASCRIPT</div>
    </div>
</section>

<section class="section section-muted" id="about">
    <div class="container two-col">
        <div><p class="section-label">01 / ABOUT ME</p><h2>Curious by nature.<br><span>Practical by design.</span></h2></div>
        <div class="about-copy">
            <p>I am a PHP Backend and WordPress/WooCommerce Developer with 3+ years of experience building e-commerce platforms and database-driven web applications for international clients in the US, UK, and Europe.</p>
            <p>I specialize in bridging frontend interfaces with robust backend architectures—including integrating external supplier REST APIs with WooCommerce (synchronizing 700–800 products daily), building high-performance server-side DataTables, engineering CSV import parsers, and crafting relational database models in MySQL and CodeIgniter.</p>
            <p>Honored as <strong>Emerging Star of the Month</strong> at Narola Infotech and <strong>Star Performer of the Year</strong> (Hall of Fame) at SmartInfosys.net, I take pride in delivering maintainable, high-quality code that solves real business challenges.</p>
            <a class="text-link" href="#contact">Have a role or project in mind? <span>↗</span></a>
        </div>
    </div>
</section>

<section class="section" id="skills">
    <div class="container">
        <div class="section-heading"><div><p class="section-label">02 / TOOLKIT</p><h2>Skills I bring <span>to the table.</span></h2></div><p class="section-intro">A proven, production-tested toolkit for building, integrating, and scaling web applications.</p></div>
        <div class="skills-grid">
            <article class="skill-card">
                <div class="skill-icon">⌘</div>
                <h3>Backend Development</h3>
                <p>PHP, CodeIgniter, REST API endpoints, database models, AJAX interactions, server-side data processing, debugging, and code reviews.</p>
                <div class="tag-list"><span>PHP</span><span>CodeIgniter</span><span>MySQL</span><span>REST APIs</span><span>AJAX</span></div>
            </article>
            <article class="skill-card">
                <div class="skill-icon">◈</div>
                <h3>WordPress & E-commerce</h3>
                <p>Custom themes and child themes, custom plugins, hooks and filters, WooCommerce checkout, payment gateways, shipping, subscriptions, ACF, and Shopify.</p>
                <div class="tag-list"><span>WordPress</span><span>WooCommerce</span><span>ACF</span><span>Shopify</span><span>LearnDash</span></div>
            </article>
            <article class="skill-card">
                <div class="skill-icon">⇄</div>
                <h3>APIs & Integrations</h3>
                <p>REST APIs, supplier and third-party API integrations, automated product data synchronization, large payload batching, and webhooks.</p>
                <div class="tag-list"><span>REST API</span><span>Supplier Sync</span><span>Webhooks</span><span>JSON</span></div>
            </article>
            <article class="skill-card">
                <div class="skill-icon">▤</div>
                <h3>Data & Deployment</h3>
                <p>Server-side DataTables, CSV import/export parsing, data validation and pagination, JavaScript, jQuery, Git version control, and cPanel deployments.</p>
                <div class="tag-list"><span>DataTables</span><span>CSV Parsing</span><span>Git</span><span>JavaScript</span><span>cPanel</span></div>
            </article>
        </div>
    </div>
</section>

<section class="section section-muted" id="projects">
    <div class="container">
        <div class="section-heading"><div><p class="section-label">03 / SELECTED WORK</p><h2>Projects with <span>real-world impact.</span></h2></div><p class="section-intro">A closer look at production systems, e-commerce stores, and enterprise workflows I've built and contributed to.</p></div>
        <div class="projects-grid">
        <?php foreach ($projects as $index => $project): ?>
            <article class="project-card">
                <div class="project-art project-art-<?php echo (int)(($index % 3) + 1); ?>">
                    <div class="project-art-top"><span><?php echo e($project['category']); ?></span><span>0<?php echo $index + 1; ?></span></div>
                    <div class="project-mock">
                        <div class="mock-sidebar"><b></b><i></i><i></i><i></i><i></i></div>
                        <div class="mock-content"><div class="mock-heading"></div><div class="mock-subheading"></div><div class="mock-stats"><i></i><i></i><i></i></div><div class="mock-table"><b></b><i></i><i></i><i></i></div></div>
                    </div>
                    <span class="project-art-caption"><?php echo e($project['category']); ?></span>
                </div>
                <div class="project-info">
                    <p class="project-type"><?php echo e($project['category']); ?></p>
                    <h3><?php echo e($project['title']); ?></h3>
                    <p><?php echo e($project['summary']); ?></p>
                    <?php if (stripos($project['title'], 'KankuDiamond') !== false): ?>
                        <a href="https://kankudiamond.com/" target="_blank" rel="noopener noreferrer" class="project-link-badge">Visit kankudiamond.com <span>↗</span></a>
                    <?php elseif (stripos($project['title'], 'Geminismart') !== false): ?>
                        <a href="https://app.geminiplatform.ai/" target="_blank" rel="noopener noreferrer" class="project-link-badge">Visit app.geminiplatform.ai <span>↗</span></a>
                    <?php endif; ?>
                    <details>
                        <summary>View architecture & implementation details <span>＋</span></summary>
                        <p><?php echo e($project['details']); ?></p>
                    </details>
                    <div class="tag-list">
                        <?php foreach (explode(',', $project['technologies']) as $tech): ?>
                            <span><?php echo e(trim($tech)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
        <p class="project-note"><strong>Additional projects delivered:</strong> <a href="https://www.dubai.pn/" target="_blank" rel="noopener noreferrer" style="color:var(--accent);">Dubai.pn ↗</a> (Houzez real estate portal), <a href="https://bodyshop.is/" target="_blank" rel="noopener noreferrer" style="color:var(--accent);">Body Shop Iceland ↗</a> (Shopify storefront), Simcoe Trenching (corporate website with SEO), and corporate CMS portals for Envirowrap &amp; Navatek Group.</p>
    </div>
</section>

<section class="section" id="experience">
    <div class="container">
        <div class="section-heading"><div><p class="section-label">04 / EXPERIENCE</p><h2>Proven track record <span>shipping work.</span></h2></div><p class="section-intro">Hands-on experience delivering e-commerce stores, business modules, and custom integrations for international clients.</p></div>
        <div class="timeline">
            <article class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-top">
                        <h3>WordPress Developer</h3>
                        <span>Narola Infotech · March 2025 – Present</span>
                    </div>
                    <p style="color: var(--accent-2); font-size: 12px; margin-bottom: 8px; font-weight: 600;">🏆 Recognition: Emerging Star of the Month</p>
                    <p>Surat, Gujarat. Developed and customized WordPress, WooCommerce, and Shopify platforms for clients in the US, UK, and European markets. Integrated supplier REST APIs with WooCommerce to automatically synchronize 700–800 products daily. Built REST API endpoints, database models, server-side DataTables, and CSV import logic for CodeIgniter applications, including key contributions to the Max ERP Estimates Module.</p>
                    <div class="tag-list"><span>WordPress</span><span>WooCommerce</span><span>PHP</span><span>CodeIgniter</span><span>REST APIs</span><span>MySQL</span><span>DataTables</span><span>Shopify</span></div>
                </div>
            </article>
            <article class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <div class="timeline-top">
                        <h3>Junior Web Developer</h3>
                        <span>SmartInfosys.net · July 2023 – February 2025</span>
                    </div>
                    <p style="color: var(--accent-2); font-size: 12px; margin-bottom: 8px; font-weight: 600;">🏆 Recognition: Star Performer of the Year · Hall of Fame</p>
                    <p>Delivered 10+ websites, including e-commerce stores, corporate portals, and WordPress CMS platforms. Customized WordPress themes, created custom plugin features, built interactive responsive frontend layouts using HTML, CSS, JavaScript, and PHP, and handled direct client communication, requirements analysis, and post-launch support.</p>
                    <div class="tag-list"><span>WordPress</span><span>PHP</span><span>JavaScript</span><span>WooCommerce</span><span>HTML5/CSS3</span><span>MemberPress</span><span>LearnDash</span></div>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="section section-muted" id="education">
    <div class="container two-col">
        <div>
            <p class="section-label">05 / EDUCATION & HONORS</p>
            <h2>Academic foundation <span>& milestones.</span></h2>
        </div>
        <div>
            <div style="background: #11141c; border: 1px solid #252b38; border-radius: 13px; padding: 28px; margin-bottom: 20px;">
                <span style="color: var(--accent); font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase;">Bachelor's Degree</span>
                <h3 style="font-family: var(--display); font-size: 20px; margin: 8px 0 6px;">B.Tech in Computer Science</h3>
                <p style="color: #9aa3b4; margin: 0 0 14px; font-size: 13px;">Parul University, Vadodara, Gujarat · <strong>2019 – 2023</strong></p>
                <p style="color: #8e98aa; font-size: 13px; margin: 0; line-height: 1.8;">Comprehensive foundation in computer science, object-oriented programming, relational databases, data structures, and web technologies.</p>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div style="background: #11141c; border: 1px solid #252b38; border-radius: 11px; padding: 18px;">
                    <strong style="display: block; color: var(--accent-2); font-size: 12px; margin-bottom: 5px;">🏆 Honors & Awards</strong>
                    <small style="color: #9aa3b4; font-size: 11px; line-height: 1.6; display: block;">Emerging Star of the Month (Narola)<br>Star Performer of the Year & Hall of Fame (SmartInfosys)</small>
                </div>
                <div style="background: #11141c; border: 1px solid #252b38; border-radius: 11px; padding: 18px;">
                    <strong style="display: block; color: var(--accent); font-size: 12px; margin-bottom: 5px;">🌐 Languages</strong>
                    <small style="color: #9aa3b4; font-size: 11px; line-height: 1.6; display: block;">English · Gujarati · Hindi</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-section section" id="contact">
    <div class="container contact-grid">
        <div>
            <p class="section-label">06 / CONTACT</p>
            <h2>Let's build something <span>useful.</span></h2>
            <p class="contact-intro">Interested in hiring a dedicated PHP Backend, WordPress, or WooCommerce developer? Reach out directly through any of the channels below.</p>
            <div class="contact-links">
                <a href="mailto:modidipen3@gmail.com">modidipen3@gmail.com <span>↗</span></a>
                <a href="https://wa.me/918200217135" target="_blank" rel="noopener noreferrer">WhatsApp (+91 82002 17135) <span>↗</span></a>
                <a href="tel:+918200217135">Call (+91 82002 17135) <span>↗</span></a>
                <a href="https://www.linkedin.com/in/dipen-modi-90062925b/" target="_blank" rel="noopener noreferrer">LinkedIn (/in/dipen-modi) <span>↗</span></a>
                <a href="https://github.com/dipen132" target="_blank" rel="noopener noreferrer">GitHub (@dipen132) <span>↗</span></a>
            </div>
        </div>
        <form class="contact-form" method="post" action="#contact">
            <h3>Send a message</h3>
            <?php if ($success): ?><div class="form-alert success"><?php echo e($success); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="form-alert error"><?php echo e($error); ?></div><?php endif; ?>
            
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['csrf_token']); ?>">
            <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off" aria-hidden="true">

            <label for="name">Your name</label>
            <input id="name" name="name" type="text" maxlength="120" required value="<?php echo e($name); ?>" placeholder="Your name or company">

            <label for="email">Email address</label>
            <input id="email" name="email" type="email" maxlength="190" required value="<?php echo e($email); ?>" placeholder="your.email@example.com">

            <label for="subject">Subject <span>(optional)</span></label>
            <input id="subject" name="subject" type="text" maxlength="190" value="<?php echo e($subject); ?>" placeholder="PHP / WordPress Developer Opportunity">

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" maxlength="5000" required placeholder="Tell me a little about your project or role..."><?php echo e($message); ?></textarea>

            <button class="button button-primary form-submit" type="submit" name="contact_submit" value="1">Send message <span>↗</span></button>
            <p class="form-footnote">Messages are saved securely. You can also reach me directly on WhatsApp at +91 82002 17135.</p>
        </form>
    </div>
</section>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <a class="brand" href="#home"><span class="brand-mark">DM</span><span>Dipen Modi<span class="brand-dot">.</span></span></a>
        <p>PHP Backend & WordPress Developer · Surat, Gujarat, India</p>
        <a href="#home">Back to top ↑</a>
    </div>
    <div class="container copyright">© <?php echo date('Y'); ?> Dipen Modi. All rights reserved.</div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
