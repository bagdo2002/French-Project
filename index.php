<?php
session_start();
require 'config.php';

$user = null;
$message = '';

if (isset($_SESSION['user_id'])) {
  $sql = "SELECT username FROM users WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([':id' => $_SESSION['user_id']]);
  $loggedInUser = $stmt->fetchColumn();

  if ($loggedInUser) {
    $user = $loggedInUser;
  } else {
    // User ID in session but not found in DB - perhaps deleted or issue
    session_destroy(); // Clear invalid session
    $message = "Your session is invalid. Please log in again.";
  }
} else {
  // No user ID in session
  $message = "Vous n'êtes pas connecté. Veuillez <a href=\"login.html\" style=\"color: var(--primary); text-decoration: none;\">vous connecter</a> ou <a href=\"inscription.html\" style=\"color: var(--primary); text-decoration: none;\">vous inscrire</a>.";
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trinidad Betting</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg-dark: #0d1117;
      --bg-card: #161b22;
      --text-light: #f2f2f2;
      --primary: #ffc845;
      --secondary: #1e8e3e;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: "Montserrat", Arial, sans-serif;
      color: var(--text-light);
    }

    body {
      background: var(--bg-dark);
    }

    /* ---------- HEADER STYLES ---------- */
    header {
      width: 100%;
      background: #121721;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .header-container {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      align-items: center;
      gap: 1rem;
      max-width: 1400px;
      margin: 0 auto;
      padding: 1rem 2rem;
    }

    .header-logo {
      height: 48px;
      justify-self: start;
    }

    .header-title {
      font-size: 1.6rem;
      font-weight: 600;
      margin: 0;
      justify-self: center;
    }

    .header-actions {
      display: flex;
      gap: 0.5rem;
      justify-self: end;
      align-items: center;
    }

    .header-actions .username {
      font-weight: 600;
      margin-right: 0.5rem;
    }

    .header-actions .btn {
      cursor: pointer;
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      background: var(--primary);
      color: #000;
      transition: transform 0.15s;
      white-space: nowrap;
      text-decoration: none;
    }

    .header-actions .btn:hover {
      transform: scale(1.05);
    }

    /* ---------- WELCOME BANNER (NEW) ---------- */
    .welcome-banner {
      background: var(--bg-card);
      text-align: center;
      padding: 1.2rem 1rem;
      border-bottom: 3px solid var(--primary);
    }

    .welcome-banner p {
      line-height: 1.5;
    }

    .welcome-banner strong {
      font-size: 1.5rem;
      font-weight: 600;
    }


    /* ---------- HERO WRAPPER (Column Layout) ---------- */
    .hero {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 2.5rem;
      padding: 3rem 2rem;
      max-width: 1400px;
      margin: 0 auto;
    }

    /* ---------- HERO SECTIONS (Right and Left) ---------- */
    .hero-left {
      order: 2;
      /* This section (testimonials) appears second */
      max-width: 540px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      gap: 2rem;
    }

    .hero-right {
      order: 1;
      /* This section (tagline/card) appears first */
      max-width: 540px;
      /* Matched width for alignment */
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.5rem;
      /* Space between tagline and card */
    }

    .tagline {
      font-size: 1.4rem;
      line-height: 1.4;
      font-weight: 600;
      max-width: 34rem;
      text-align: center;
    }

    /* --- Testimonials (FIXED SIZE GUARANTEED) --- */
    .testimonials {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      width: 100%;
    }

    .arrow {
      background: transparent;
      border: none;
      font-size: 2rem;
      cursor: pointer;
      color: var(--primary);
      transition: transform 0.15s;
      flex-shrink: 0;
    }

    .arrow:hover {
      transform: scale(1.15);
    }

    .testimonial {
      flex: 1;
      background: var(--bg-card);
      border-left: 4px solid var(--primary);
      padding: 1rem 1.2rem;
      border-radius: 8px;
      height: 140px;
      /* Fixed height for desktop */
      display: flex;
      flex-direction: column;
      justify-content: center;
      overflow: hidden;
    }

    .quote {
      font-size: 1rem;
      margin-bottom: 0.6rem;
    }

    .author {
      font-size: 0.9rem;
      font-weight: 600;
      text-align: right;
    }

    /* ---------- CARD STYLES ---------- */
    .card {
      background: var(--bg-card);
      border-radius: 12px;
      padding: 2.5rem 2rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
      width: 100%;
      max-width: 500px;
      /* Max-width for the card itself */
    }

    .card h2 {
      font-size: 1.4rem;
      margin-bottom: 1.2rem;
      text-align: center;
    }

    .markets {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 1rem;
      justify-content: center;
    }

    .market-btn {
      background: #2d333b;
      border: 1px solid #444c56;
      padding: 0.4rem 0.8rem;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: background 0.2s;
    }

    .market-btn.active,
    .market-btn:hover {
      background: var(--secondary);
    }

    .caracs {
      margin: 0.5rem 0 1.5rem 0;
    }

    .caracs h3,
    .simulator h3 {
      font-size: 1rem;
      margin-bottom: 0.4rem;
      text-align: center;
    }

    .caracs ul,
    .simulator ul {
      list-style: none;
    }

    .caracs li,
    .simulator li {
      margin: 0.25rem 0;
      font-size: 0.9rem;
      text-align: center;
    }

    .simulator {
      margin-bottom: 1.5rem;
    }

    .simulator label {
      font-size: 0.9rem;
      display: block;
      margin-bottom: 0.3rem;
      text-align: center;
    }

    .simulator input[type="range"] {
      width: 100%;
      -webkit-appearance: none;
      appearance: none;
      height: 6px;
      background: #444c56;
      border-radius: 3px;
      outline: none;
    }

    .simulator input[type="range"]::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: var(--primary);
      cursor: pointer;
      border: none;
    }

    .cart {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 0.8rem;
    }

    .cart .price {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .cart-btn {
      background: var(--primary);
      color: #000;
      padding: 0.6rem 1.2rem;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      flex: 1 1 140px;
    }

    /* ---------- FOOTER ---------- */
    footer {
      text-align: center;
      padding: 2rem 1rem;
      margin-top: 2rem;
      background: #121721;
      font-size: 0.9rem;
      color: #888;
    }

    /* ---------- RESPONSIVE STYLES ---------- */
    @media (max-width: 768px) {
      .hero {
        padding: 2rem 1rem;
      }

      .tagline {
        font-size: 1.2rem;
      }

      .testimonials {
        margin-top: 1.5rem;
      }

      .testimonial {
        height: auto;
        /* Allow height to adjust to content on small screens */
        min-height: 120px;
        /* But ensure a minimum height */
      }

      /* --- Mobile Header Styles --- */
      header {
        padding: 0;
      }

      .header-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.5rem 1rem;
      }

      .header-logo {
        order: 1;
      }

      .header-actions {
        order: 2;
      }

      .header-title {
        order: 3;
        width: 100%;
        text-align: center;
        font-size: 1.4rem;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="header-container">
      <img style="border-radius: 50%; cursor: pointer" src="./logo.jpg" alt="Logo Trinidad" class="header-logo" />
      <h1 class="header-title">Trinidad Betting</h1>
      <div class="header-actions">
        <?php if ($user): ?>
          <span class="username"><?= htmlspecialchars($user) ?></span>
          <a class="btn" href="logout.php">Déconnexion</a>
        <?php else: ?>
          <a class="btn" href="login.html">Connexion</a>
          <a class="btn" onclick="location.href='inscription.html'">S'inscrire</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <?php if ($user): ?>
    <div class="welcome-banner">
      <p>
        <strong>Bienvenue <?= htmlspecialchars($user) ?> !</strong><br>
        Vos abonnements en cours : Aucun
      </p>
    </div>
  <?php endif; ?>

  <?php if ($message && !$user): ?>
    <div style="color: var(--text-light); text-align: center; padding: 10px; margin: 10px auto; max-width: 800px;">
      <?= $message ?>
    </div>
  <?php endif; ?>

  <main class="hero">
    <section class="hero-right">
      <p class="tagline">
        Plusieurs centaines de milliers d'euros gagnés chaque année :
        transformez votre passion pour le sport en investissement rentable.
      </p>
      <div class="card">
        <h2>Nos offres</h2>
        <div class="markets">
          <button class="market-btn active" data-market="defis">Défis</button>
          <button class="market-btn" data-market="cote">Côte Boostée</button>
          <button class="market-btn" data-market="nba">NBA</button>
          <button class="market-btn" data-market="moneyline">
            Moneyline
          </button>
          <button class="market-btn" data-market="autres">
            Autres marchés
          </button>
        </div>

        <div class="caracs">
          <h3>Caractéristiques</h3>
          <ul>
            <li id="roi">ROI : +25%</li>
            <li id="mise">Mise moyenne : 50 €</li>
            <li id="nb">Nombre de paris moyen : 30/mois</li>
            <li id="cout">Coût abonnement : 49 €/mois</li>
          </ul>
        </div>

        <div class="simulator">
          <h3>Simulateur de gain</h3>
          <label for="bankroll">Bankroll (€) : <span id="bankrollValue">1000</span></label>
          <input type="range" id="bankroll" min="100" max="20000" step="100" value="1000" />
          <ul>
            <li id="gainMois">Gain estimé par mois : 250 €</li>
            <li id="gainAn">Gain estimé par an : 3 000 €</li>
          </ul>
        </div>

        <div class="cart">
          <span class="price" id="price">49 €</span>
          <button class="cart-btn" id="addCart">Ajouter au panier 🛒</button>
        </div>
      </div>
    </section>

    <section class="hero-left">
      <div class="testimonials">
        <button class="arrow" id="prevT">&#8249;</button>
        <div class="testimonial" id="testimonialBox">
          <p class="quote">
            « J'ai triplé ma bankroll en six mois grâce aux défis ! »
          </p>
          <p class="author">- Antoine B.</p>
        </div>
        <button class="arrow" id="nextT">&#8250;</button>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Trinidad Betting. All Rights Reserved.</p>
  </footer>

  <script>
    /* OFFERS DATA */
    const offers = {
      defis: {
        roi: "+25%",
        mise: "50 €",
        nb: "30/mois",
        cout: "49 €/mois",
        price: "49 €",
      },
      cote: {
        roi: "+40%",
        mise: "20 €",
        nb: "10/mois",
        cout: "29 €/mois",
        price: "29 €",
      },
      nba: {
        roi: "+32%",
        mise: "30 €",
        nb: "25/mois",
        cout: "39 €/mois",
        price: "39 €",
      },
      moneyline: {
        roi: "+18%",
        mise: "100 €",
        nb: "15/mois",
        cout: "59 €/mois",
        price: "59 €",
      },
      autres: {
        roi: "+22%",
        mise: "40 €",
        nb: "20/mois",
        cout: "35 €/mois",
        price: "35 €",
      },
    };

    const bankrollInput = document.getElementById("bankroll");
    const bankrollValueSpan = document.getElementById("bankrollValue");
    const gainMoisEl = document.getElementById("gainMois");
    const gainAnEl = document.getElementById("gainAn");
    let currentROI = 25;

    function formatEuro(num) {
      return num.toLocaleString("fr-FR", {
        style: "currency",
        currency: "EUR",
        minimumFractionDigits: 0,
      });
    }

    function updateGains() {
      const bankroll = parseInt(bankrollInput.value, 10);
      bankrollValueSpan.textContent = bankroll;
      const monthlyGain = (bankroll * currentROI) / 100;
      const annualGain = monthlyGain * 12;
      gainMoisEl.textContent = `Gain estimé par mois : ${formatEuro(
        monthlyGain
      )}`;
      gainAnEl.textContent = `Gain estimé par an : ${formatEuro(annualGain)}`;
    }

    bankrollInput.addEventListener("input", updateGains);

    document.querySelectorAll(".market-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        document
          .querySelectorAll(".market-btn")
          .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        const key = btn.dataset.market;
        const data = offers[key];
        document.getElementById("roi").textContent = `ROI : ${data.roi}`;
        document.getElementById(
          "mise"
        ).textContent = `Mise moyenne : ${data.mise}`;
        document.getElementById(
          "nb"
        ).textContent = `Nombre de paris moyen : ${data.nb}`;
        document.getElementById(
          "cout"
        ).textContent = `Coût abonnement : ${data.cout}`;
        document.getElementById("price").textContent = data.price;
        currentROI = parseFloat(data.roi);
        updateGains();
      });
    });

    updateGains();

    document.getElementById("addCart").addEventListener("click", () => {
      // In a real application, you would add the item to a cart.
      // For this example, we'll just show a message.
      const cartButton = document.getElementById("addCart");
      cartButton.textContent = "Ajouté !";
      setTimeout(() => {
        cartButton.innerHTML = "Ajouter au panier 🛒";
      }, 2000);
    });

    /* ---------- TESTIMONIALS ---------- */
    const testimonials = [
      {
        quote: "« J'ai triplé ma bankroll en six mois grâce aux défis ! »",
        author: "- Antoine B.",
      },
      {
        quote:
          "« Les analyses NBA sont incroyablement précises : +35% sur ma saison »",
        author: "- Laura M.",
      },
      {
        quote: "« La côte boostée a payé mes vacances d'été ! »",
        author: "- Marc L.",
      },
      {
        quote:
          "« Service au top et community manager très réactif. Je recommande ! »",
        author: "- Sofia G.",
      },
    ];
    let currentTestimonial = 0;
    const testimonialBox = document.getElementById("testimonialBox");

    function renderTestimonial(idx) {
      testimonialBox.querySelector(".quote").textContent =
        testimonials[idx].quote;
      testimonialBox.querySelector(".author").textContent =
        testimonials[idx].author;
    }

    document.getElementById("prevT").addEventListener("click", () => {
      currentTestimonial =
        (currentTestimonial - 1 + testimonials.length) % testimonials.length;
      renderTestimonial(currentTestimonial);
    });
    document.getElementById("nextT").addEventListener("click", () => {
      currentTestimonial = (currentTestimonial + 1) % testimonials.length;
      renderTestimonial(currentTestimonial);
    });
  </script>
</body>

</html>