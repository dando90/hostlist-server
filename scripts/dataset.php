<?php
declare(strict_types=1);

const DATASET_SEED = 'hostlist-synthetic-europe-v1';
const DATASET_SIZE = 500;

function choose(array $items, string $key): mixed
{
    $index = hexdec(substr(hash('sha256', DATASET_SEED . '|' . $key), 0, 8)) % count($items);
    return $items[$index];
}

function cityRows(): array
{
    $groups = [
        ['Italy', 'IT', ['Italian', 'English'], [
            ['Milan', 48], ['Rome', 38], ['Bologna', 20], ['Turin', 16], ['Florence', 12], ['Naples', 10], ['Parma', 6],
        ]],
        ['Spain', 'ES', ['Spanish', 'English'], [
            ['Madrid', 38], ['Barcelona', 32], ['Valencia', 12], ['Seville', 7], ['Bilbao', 6],
        ]],
        ['France', 'FR', ['French', 'English'], [
            ['Paris', 42], ['Lyon', 17], ['Toulouse', 12], ['Nantes', 10], ['Bordeaux', 8], ['Marseille', 6],
        ]],
        ['Germany', 'DE', ['German', 'English'], [
            ['Berlin', 36], ['Munich', 22], ['Hamburg', 14], ['Cologne', 12], ['Frankfurt', 11],
        ]],
        ['Netherlands', 'NL', ['Dutch', 'English'], [
            ['Amsterdam', 27], ['Rotterdam', 14], ['Utrecht', 9], ['Eindhoven', 8], ['The Hague', 7],
        ]],
    ];
    $rows = [];
    foreach ($groups as [$country, $code, $languages, $cities]) {
        foreach ($cities as [$city, $count]) {
            for ($i = 0; $i < $count; $i++) $rows[] = compact('country', 'code', 'languages', 'city');
        }
    }
    if (count($rows) !== DATASET_SIZE) throw new RuntimeException('City allocation must equal dataset size.');
    return $rows;
}

function archetypes(): array
{
    return [
        [
            'name' => 'product studio', 'domain' => 'Software Development', 'secondary' => ['Web Development', 'Product Design', 'Quality Assurance'],
            'stack' => ['TypeScript and React', 'PHP and Laravel-style patterns', 'Python services and PostgreSQL', 'Kotlin APIs and cloud queues', 'Go microservices and observability', 'Vue interfaces and Node.js'],
            'problems' => ['a multi-tenant planning workspace', 'a subscription analytics console', 'a collaboration tool for distributed teams', 'a workflow builder for small organisations', 'a mobile companion for field specialists'],
            'work' => ['prototype a focused feature with a product pair', 'improve an accessible component and its tests', 'trace a slow request through the service boundary', 'turn a customer interview into a small product experiment', 'document a release path and automate one manual step'],
        ],
        [
            'name' => 'web engineering studio', 'domain' => 'Web Development', 'secondary' => ['E-commerce', 'Accessibility', 'Performance Engineering'],
            'stack' => ['React, CSS and browser tooling', 'PHP, MySQL and progressive enhancement', 'Astro, content modelling and edge caching', 'Vue, design tokens and component tests', 'Node.js, REST endpoints and Playwright'],
            'problems' => ['a multilingual commerce storefront', 'an accessible booking flow', 'a content platform for independent publishers', 'a high-traffic catalogue with search facets', 'a set of reusable pages for a civic service'],
            'work' => ['refine keyboard navigation across a real flow', 'profile a page and reduce its loading cost', 'build a reusable component with visual checks', 'connect a small frontend change to a tested API', 'map content fields into a more reliable publishing workflow'],
        ],
        [
            'name' => 'data and AI lab', 'domain' => 'Data Science & AI', 'secondary' => ['Data Engineering', 'Machine Learning', 'Analytics'],
            'stack' => ['Python, SQL and reproducible notebooks', 'dbt models and warehouse testing', 'scikit-learn pipelines with model cards', 'Python APIs, vector search and evaluation sets', 'Airflow-style orchestration and data quality checks'],
            'problems' => ['a transparent demand-forecasting service', 'a document classification workflow', 'a quality monitor for public datasets', 'a recommendation prototype with measurable baselines', 'an internal search assistant with auditable sources'],
            'work' => ['compare a baseline with a carefully measured alternative', 'add a data-quality check before a model run', 'write an evaluation set that exposes failure cases', 'turn a notebook experiment into a reproducible pipeline', 'summarise model limits for a product teammate'],
        ],
        [
            'name' => 'cybersecurity cooperative', 'domain' => 'Cybersecurity', 'secondary' => ['Cloud Security', 'Application Security', 'Incident Response'],
            'stack' => ['Python automation and threat modelling', 'Linux hardening and container scanning', 'OWASP testing and secure code review', 'cloud identity policy and audit trails', 'log analysis, detection rules and incident notes'],
            'problems' => ['a secure onboarding path for a SaaS service', 'a dependency-risk dashboard', 'a small incident-response exercise environment', 'a cloud permission review toolkit', 'a practical application-security training lab'],
            'work' => ['reproduce a safe lab finding and write a remediation note', 'add a security check to a development pipeline', 'review a permission boundary with a mentor', 'turn a threat scenario into a detection rule', 'improve a deliberately vulnerable training exercise'],
        ],
        [
            'name' => 'cloud platform workshop', 'domain' => 'Cloud Computing', 'secondary' => ['DevOps', 'Site Reliability Engineering', 'Platform Engineering'],
            'stack' => ['Terraform, containers and GitHub Actions', 'Kubernetes manifests and service metrics', 'AWS-style infrastructure and Python automation', 'Linux, shell scripting and deployment checks', 'OpenTelemetry, dashboards and incident runbooks'],
            'problems' => ['a self-service deployment portal', 'a resilient event-processing service', 'a lower-cost test environment', 'a service health dashboard for small teams', 'a repeatable release pipeline for container apps'],
            'work' => ['make an environment reproducible from a clean checkout', 'add a useful signal to a service dashboard', 'automate a safe deployment check', 'document a recovery exercise and its owner actions', 'measure resource use before changing infrastructure'],
        ],
        [
            'name' => 'digital design engineering studio', 'domain' => 'UX/UI Design', 'secondary' => ['Frontend Development', 'Accessibility', 'Design Systems'],
            'stack' => ['Figma prototypes with semantic HTML', 'design tokens, CSS and Storybook', 'React components with accessibility checks', 'user research notes and lightweight analytics', 'responsive layouts and browser testing'],
            'problems' => ['a design system for a growing service', 'a simpler onboarding journey', 'a dashboard for first-time data users', 'a mobile-first public information service', 'a tested component library for product teams'],
            'work' => ['prototype two alternatives and test them with colleagues', 'translate a design token into a robust component', 'audit a key path with keyboard and screen-reader checks', 'connect a usability observation to a small interface change', 'document a component decision for engineers and designers'],
        ],
        [
            'name' => 'open-source software collective', 'domain' => 'Open Source Software', 'secondary' => ['Software Development', 'Developer Tools', 'Documentation'],
            'stack' => ['Python packages and CI workflows', 'TypeScript developer tools and release automation', 'Rust utilities with cross-platform tests', 'Java libraries and API documentation', 'Git, issue triage and reproducible builds'],
            'problems' => ['a maintainer tool for safer releases', 'a small library used by civic organisations', 'a command-line helper for data cleanup', 'a test harness for a widely used parser', 'a contribution guide for a community project'],
            'work' => ['pair on a well-scoped issue from reproduction to review', 'improve an example and verify it against the current API', 'add a regression test for a reported edge case', 'make a release task repeatable for maintainers', 'write a contributor note from a newcomer perspective'],
        ],
        [
            'name' => 'digital health software studio', 'domain' => 'Health Informatics', 'secondary' => ['Data Engineering', 'Privacy Engineering', 'Web Development'],
            'stack' => ['TypeScript interfaces and privacy-aware APIs', 'Python data pipelines with audit logging', 'PostgreSQL, access controls and integration tests', 'FHIR-inspired data modelling and validation', 'accessibility checks and secure session handling'],
            'problems' => ['a consent-aware appointment coordination tool', 'a de-identified service analytics dashboard', 'a reliable integration test sandbox', 'an accessible patient information portal', 'a staff workflow for reviewing digital referrals'],
            'work' => ['validate synthetic records against clear data rules', 'improve an accessible interface using safe test data', 'add an audit event to a non-sensitive workflow', 'test an integration boundary with synthetic fixtures', 'document privacy assumptions for a technical change'],
        ],
        [
            'name' => 'geospatial software lab', 'domain' => 'Geospatial Technology', 'secondary' => ['Data Visualization', 'Web Development', 'Data Engineering'],
            'stack' => ['Python, GeoJSON and spatial SQL', 'TypeScript maps and vector tiles', 'PostGIS queries and data validation', 'Python raster processing and reproducible jobs', 'accessible map interfaces and API tests'],
            'problems' => ['a public-transport accessibility map', 'a neighbourhood change explorer', 'a data pipeline for open geographic layers', 'a route-planning prototype for community services', 'a map interface for climate adaptation scenarios'],
            'work' => ['check a spatial join against hand-verified examples', 'make a map interaction usable without a pointer', 'profile a tile request and explain its bottleneck', 'add provenance notes to an open-data pipeline', 'compare two visual encodings with a small user test'],
        ],
        [
            'name' => 'developer tools atelier', 'domain' => 'Developer Tools', 'secondary' => ['Software Development', 'Quality Assurance', 'Cloud Computing'],
            'stack' => ['TypeScript editor extensions and language services', 'Python command-line tools and test fixtures', 'Go build utilities with structured logs', 'JavaScript test runners and browser automation', 'Rust performance tools and cross-platform CI'],
            'problems' => ['a faster feedback loop for frontend teams', 'a clear diagnostic tool for build failures', 'a test fixture generator for API clients', 'a local preview workflow for documentation', 'a small profiler for repetitive developer tasks'],
            'work' => ['reduce a confusing diagnostic to a useful reproducible case', 'add a focused test around a developer-facing edge case', 'measure a command before optimising its slow path', 'improve an example so a new user succeeds quickly', 'connect a tool change to a short, verifiable workflow'],
        ],
    ];
}

function textChoices(): array
{
    return [
        'openers' => ['Our small team is currently shaping', 'We are iterating on', 'A cross-functional group is maintaining', 'This season we are extending', 'The studio has recently started rebuilding', 'Our engineers and product partners are testing', 'A compact project crew is improving', 'We are preparing the next version of'],
        'closing' => ['We prefer small changes that can be reviewed with the people who will use them.', 'The work is scoped so a learner can own a useful slice and explain the trade-offs.', 'Progress is discussed openly, with room to question assumptions and revise the approach.', 'A practical outcome matters more than a polished presentation.', 'We keep the backlog flexible and choose a contribution together after an initial conversation.', 'The team values readable decisions, careful feedback and steady delivery.'],
        'motivation' => ['I enjoy helping people connect technical choices to the needs behind a product.', 'I like creating a calm space where a newcomer can ask precise questions and try an idea.', 'Sharing the reasoning behind my work is one of the most useful parts of collaborating.', 'I am motivated by seeing someone move from a first experiment to a contribution they can explain.', 'Good mentoring makes room for curiosity, disagreement and a second attempt.', 'I want our team habits to be understandable to someone joining from another country or background.', 'I find it rewarding when a small piece of guidance helps a learner become more independent.', 'We would like to exchange practical knowledge while building something that has a clear user.'],
        'motivation_additions' => ['I especially enjoy pairing on the first working version.', 'I like making unfamiliar systems easier to navigate.', 'I am happiest when a question turns into a small experiment.', 'I care about leaving behind notes that make the next contribution easier.', 'I appreciate a fresh perspective on decisions we have become used to.', 'I want technical knowledge to travel across the whole team.', 'I value patient reviews that explain the reasoning as well as the result.', 'I enjoy helping people find a task that stretches them without overwhelming them.', 'I like connecting a learner with the product context behind a ticket.', 'I believe a thoughtful handover is part of a good technical contribution.', 'I make time for practical feedback, not just a final review.', 'I enjoy comparing approaches before settling on the simplest useful one.', 'I want people to feel comfortable proposing a different solution.', 'I like turning a rough idea into a concrete first milestone.', 'I learn from seeing how someone new interprets an old workflow.', 'I care about building habits that make collaboration more inclusive.', 'I enjoy explaining trade-offs using examples from the work at hand.', 'I prefer shared understanding over clever code that only one person can maintain.', 'I want a learner to finish with a contribution they can confidently describe.', 'I see mentoring as a two-way exchange of questions and methods.'],
        'exchange_goals' => ['The exchange can include a short demo of the result to the wider team.', 'We can reserve time to compare the first idea with what testing reveals.', 'A useful part of the experience is seeing how a team makes a decision together.', 'We are happy to explain the context behind the work before choosing a task.', 'The learner can suggest a direction after exploring the product and its users.', 'We make room for an honest discussion of what did not work as expected.', 'Progress can be shared in a lightweight written update as well as a demo.', 'There is space to learn the release and review habits used by the team.', 'We can shape the collaboration around the learner’s strengths and questions.', 'The project gives a chance to see feedback turn into a better next iteration.', 'We value a final handover that captures both the result and the learning.', 'A small retrospective helps us improve the experience for the next person.', 'We can introduce the people who use the product when that helps with context.', 'The contribution should be visible enough for the learner to explain with confidence.', 'We make space for independent exploration followed by a thoughtful review.', 'The team is open to revisiting assumptions as new evidence appears.', 'A short walkthrough can connect the technical work to its practical purpose.', 'We want the collaboration to leave behind something useful for both sides.', 'The first milestone can be adjusted once the learner sees the codebase.', 'We encourage sharing questions early, before they turn into blockers.'],
        'mentoring' => ['A weekly pairing session is available, with short async reviews between meetings.', 'You can choose between guided pairing and a small independent task with two review checkpoints.', 'The first days focus on setup and context; after that we agree on a deliverable together.', 'A rotating buddy can help with onboarding, while the project lead reviews technical decisions.', 'We use short demos and written notes so learning does not depend on being in one meeting.', 'Mentoring is flexible: code review, design critique or problem-solving sessions depending on the task.', 'The team offers a weekly check-in and encourages questions in a shared project channel.', 'A senior teammate will help define a bounded task and give feedback at each milestone.'],
        'skill_practices' => ['pair debugging and small pull requests', 'API design, test strategy and code review', 'data validation and reproducible experiments', 'accessible interaction patterns and browser testing', 'threat modelling and safe test fixtures', 'continuous integration and release notes', 'query profiling and clear technical documentation', 'component design and visual regression checks', 'service metrics and incident learning', 'user interviews translated into technical acceptance criteria', 'version control workflows and maintainable handovers', 'privacy-aware data modelling and audit trails', 'performance budgets and progressive enhancement', 'automated checks with actionable failure messages', 'iterative prototyping and peer feedback', 'container workflows and environment troubleshooting'],
        'outcomes' => ['a tested feature that can be included in the next release', 'a documented experiment with its limitations and next steps', 'a small tool or workflow the team can keep using', 'a clear before-and-after measure for a real user journey', 'a maintainable contribution with tests and a short handover note', 'a prototype reviewed by teammates and a small set of intended users', 'an improvement that reduces avoidable manual work', 'a reusable example that helps the next contributor get started'],
        'impact' => ['fewer confusing steps for first-time users', 'more reliable feedback before a change reaches production', 'less repeated manual checking for the product team', 'a clearer view of where the workflow needs improvement', 'better access for people using assistive technology', 'more predictable releases with fewer last-minute surprises', 'safer handling of test data and technical changes', 'faster discovery of issues while they are still easy to fix', 'a more approachable path for a new team member', 'better evidence for choosing the next product improvement'],
        'formats' => [
            'We are looking for someone who can', 'A useful contribution could be to', 'Depending on your interests, you might', 'The initial task could focus on', 'There is room to', 'One possible starting point is to', 'We can shape a first milestone around', 'You would help the team'],
        'joiners' => [
            'A student who enjoys careful problem solving will fit well.', 'Curiosity and clear communication matter more than knowing every tool already.', 'We welcome someone who likes asking why a workflow behaves as it does.', 'A learner with an interest in practical teamwork will have room to contribute.', 'The project suits someone who wants feedback and is comfortable sharing progress.', 'We value thoughtful questions, small experiments and a willingness to document learning.', 'You do not need to arrive as an expert; we agree a realistic scope together.', 'Someone who can explain their reasoning will find plenty to explore here.',
        ],
        'impact_frames' => ['This should help', 'The expected benefit is', 'A good result would mean', 'We hope this change will support', 'The team will measure success through', 'The practical value is', 'We want the work to improve', 'A useful outcome would give'],
    ];
}

function practicesFor(string $domain): array
{
    return match ($domain) {
        'Software Development' => ['pair debugging and small pull requests', 'API design, test strategy and code review', 'service boundaries and maintainable handovers', 'product experiments and technical acceptance criteria', 'continuous integration and release notes', 'performance profiling and clear technical documentation'],
        'Web Development' => ['keyboard navigation and browser testing', 'accessible interaction patterns and semantic markup', 'component design and visual regression checks', 'performance budgets and progressive enhancement', 'content modelling and responsive layouts', 'automated checks with actionable browser failures'],
        'Data Science & AI' => ['data validation and reproducible experiments', 'model evaluation and failure analysis', 'warehouse testing and documented transformations', 'privacy-aware data modelling and audit trails', 'baseline comparisons and clear visualisation', 'pipeline monitoring and dataset provenance'],
        'Cybersecurity' => ['threat modelling and safe test fixtures', 'secure code review and dependency analysis', 'identity policy and least-privilege checks', 'detection rules and incident documentation', 'application testing in isolated labs', 'remediation tracking and security automation'],
        'Cloud Computing' => ['infrastructure as code and environment reproducibility', 'container workflows and deployment checks', 'service metrics and incident learning', 'resource profiling and cost-aware changes', 'release automation and recovery runbooks', 'reliable build pipelines and operational notes'],
        'UX/UI Design' => ['user interviews translated into interface decisions', 'design tokens and component documentation', 'accessibility audits and assistive-technology checks', 'iterative prototyping and peer feedback', 'visual regression checks and responsive layouts', 'usability observations and design-system contributions'],
        'Open Source Software' => ['issue triage and reproducible bug reports', 'cross-platform testing and release automation', 'API documentation and newcomer-friendly examples', 'maintainer workflows and regression tests', 'version control practices and contributor handovers', 'reviewable patches and community feedback'],
        'Health Informatics' => ['privacy-aware data modelling and audit trails', 'synthetic fixtures and integration testing', 'accessibility checks for information workflows', 'data validation and consent-aware interfaces', 'secure session handling and clear technical notes', 'reproducible analytics using de-identified records'],
        'Geospatial Technology' => ['spatial SQL and provenance checks', 'map accessibility and visual encoding reviews', 'GeoJSON validation and reproducible processing', 'tile performance profiling and API tests', 'open-data pipelines and metadata documentation', 'coordinate-system checks and clear map interactions'],
        'Developer Tools' => ['reproducible build diagnostics and focused test cases', 'command-line usability and actionable error messages', 'cross-platform CI and release notes', 'editor integrations and API documentation', 'performance profiling for developer workflows', 'fixture design and browser automation'],
        default => throw new InvalidArgumentException('Unknown IT domain: ' . $domain),
    };
}

function makeProfiles(): array
{
    $cities = cityRows();
    $types = archetypes();
    $words = textChoices();
    $firstNames = ['Ari', 'Bela', 'Ciro', 'Dara', 'Elio', 'Fia', 'Gio', 'Hana', 'Ivo', 'Juno', 'Kiro', 'Lina', 'Miro', 'Neri', 'Ola', 'Pia', 'Quin', 'Rumi', 'Sami', 'Tavi', 'Uma', 'Vero', 'Wren', 'Xavi', 'Yara', 'Zeno'];
    $lastNames = ['Quibble', 'Zorbin', 'Melliflux', 'Puddlewick', 'Noodleton', 'Wobbleby', 'Crinklepop', 'Fuzzleford', 'Doodlefern', 'Bramblebit', 'Tinkerloop', 'Wizzlebrook', 'Pipkinvale', 'Jumblejoy', 'Mirthspark', 'Crumblebyte', 'Fablewhisk', 'Twiddlemap', 'Bumblequartz', 'Poppetflux'];
    $middleNames = ['Cloud', 'Pebble', 'Waffle', 'Moss', 'Comet', 'Puddle', 'Marble', 'Kite', 'Lantern', 'Button', 'Clover', 'Tinsel', 'Pancake', 'Meadow', 'Otter', 'Pixel', 'Sprocket', 'Marmalade', 'Telescope', 'Sprout', 'Velvet', 'Biscuit', 'Doodle', 'Moonbeam'];
    $companyShapes = ['Imaginary {a} {b} Works', 'Fictional {a} {b} Lab', 'Whimsy {a} {b} Collective', 'Cloudberry {a} {b} Atelier', 'Make-Believe {a} {b} Studio', 'Fable {a} {b} Cooperative'];
    $companyA = ['Pixel', 'Mossy', 'Copper', 'Velvet', 'Comet', 'Marble', 'Blueberry', 'Lantern', 'Pocket', 'Sunbeam', 'Waffle', 'Cobalt', 'Pebble', 'Moonlit', 'Bramble', 'Paper'];
    $companyB = ['Badger', 'Orchard', 'Teapot', 'Kite', 'Otter', 'Accordion', 'Turnip', 'Meadow', 'Sparrow', 'Pancake', 'Marmalade', 'Clover', 'Telescope', 'Pigeon', 'Button', 'Cloud'];
    $rows = [];
    foreach ($cities as $i => $place) {
        $id = $i + 1;
        $type = $types[hexdec(substr(hash('sha256', DATASET_SEED . '|type|' . $id), 0, 8)) % count($types)];
        $first = choose($firstNames, "first|$id");
        $last = choose($lastNames, "last|$id");
        $middle = choose($middleNames, "middle|$id");
        $company = strtr(choose($companyShapes, "shape|$id"), [
            '{a}' => choose($companyA, "company-a|$id"), '{b}' => choose($companyB, "company-b|$id"),
        ]) . ' ' . str_pad((string)$id, 3, '0', STR_PAD_LEFT);
        $stackOne = choose($type['stack'], "stack-one|$id");
        $stackTwo = choose(array_values(array_diff($type['stack'], [$stackOne])), "stack-two|$id");
        $problem = choose($type['problems'], "problem|$id");
        $work = choose($type['work'], "work|$id");
        $opener = choose($words['openers'], "opener|$id");
        $format = choose($words['formats'], "format|$id");
        $outcome = choose($words['outcomes'], "outcome|$id");
        $mentor = choose($words['mentoring'], "mentor|$id");
        $motivation = choose($words['motivation'], "motivation|$id");
        $motivationAddition = choose($words['motivation_additions'], "motivation-addition|$id");
        $exchangeGoal = choose($words['exchange_goals'], "exchange-goal|$id");
        $closing = choose($words['closing'], "closing|$id");
        $skillPractice = choose(practicesFor($type['domain']), "skill-practice|$id");
        $skillPracticeTwo = choose(array_values(array_diff(practicesFor($type['domain']), [$skillPractice])), "skill-practice-two|$id");
        $impactFrame = choose($words['impact_frames'], "impact-frame|$id");
        $impactA = choose($words['impact'], "impact-a|$id");
        $impactB = choose(array_values(array_diff($words['impact'], [$impactA])), "impact-b|$id");
        $joiner = choose($words['joiners'], "joiner|$id");
        $experience = 2 + (hexdec(substr(hash('sha256', DATASET_SEED . '|experience|' . $id), 0, 8)) % 131) / 10;
        $teamSize = 3 + (hexdec(substr(hash('sha256', DATASET_SEED . '|team|' . $id), 0, 8)) % 38);
        $languageList = $place['languages'];
        if ($place['country'] !== 'Netherlands' && $id % 4 === 0) $languageList[] = 'French';
        if ($id % 3 === 0) $languageList[] = 'Spanish';
        $languageList = array_values(array_unique($languageList));
        $secondaryDomain = choose($type['secondary'], "secondary|$id");

        $rows[] = [
            'host' => [
                'id' => $id,
                'fullname' => "$first $middle $last",
                'company' => $company,
                'country' => $place['country'],
                'city' => $place['city'],
                'primary_domain' => $type['domain'],
                'secondary_domain' => $secondaryDomain,
                'primary_country' => $place['country'],
                'secondary_country' => $place['country'],
                'languages' => implode(', ', $languageList),
            ],
            'detail' => [
                'host_experience_years' => number_format($experience, 1, '.', ''),
                'team_size' => $teamSize,
                'motivation' => "$motivation $motivationAddition $exchangeGoal $closing",
                'specific_skills' => "$stackOne; $stackTwo; $skillPractice; $skillPracticeTwo; clear technical notes.",
                'offer_extra_info' => "$opener $problem. $joiner $format $work. The first milestone can lead to $outcome. $mentor",
                'business_impact' => [
                    "$impactFrame $impactA.",
                    "A related benefit is $impactB.",
                    "The team is exploring $problem, so the result can inform a practical next step.",
                ],
            ],
        ];
    }
    return $rows;
}
