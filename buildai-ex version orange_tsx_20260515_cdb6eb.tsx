// app/page.tsx
'use client';
import { useState } from 'react';
import Editor from '@/components/Editor';
import AuthButton from '@/components/AuthButton'; // si vous avez l'authentification

export default function HomePage() {
  const [profile, setProfile] = useState('autoentrepreneur');
  const [description, setDescription] = useState('');
  const [loading, setLoading] = useState(false);
  const [siteHtml, setSiteHtml] = useState('');
  const [showEditor, setShowEditor] = useState(false);

  const generate = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/generate', {
        method: 'POST',
        body: JSON.stringify({ profile, description }),
        headers: { 'Content-Type': 'application/json' },
      });
      const data = await res.json();
      if (data.html) {
        setSiteHtml(data.html);
        setShowEditor(true);
        document.getElementById('editor-section')?.scrollIntoView({ behavior: 'smooth' });
      } else {
        alert('Erreur : ' + (data.error || 'Génération échouée'));
      }
    } catch {
      alert('Erreur réseau');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-gray-50 font-sans antialiased">
      {/* Navigation (orange) */}
      <nav className="bg-white shadow-md sticky top-0 z-50">
        <div className="container mx-auto px-6 py-4 flex justify-between items-center flex-wrap">
          <div className="text-2xl font-bold text-orange-600">BuildAI-Ex</div>
          <div className="flex items-center space-x-4">
            <div className="space-x-6 hidden md:block">
              <a href="#features" className="text-gray-700 hover:text-orange-600 transition">Fonctionnalités</a>
              <a href="#pricing" className="text-gray-700 hover:text-orange-600 transition">Tarifs</a>
              <a href="#contact" className="text-gray-700 hover:text-orange-600 transition">Contact</a>
            </div>
            <AuthButton />
            <a href="#generator" className="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
              Essayer gratuitement
            </a>
          </div>
        </div>
      </nav>

      {/* Hero (orange dynamique) */}
      <section className="relative overflow-hidden bg-gradient-to-br from-orange-600 via-orange-500 to-amber-500 text-white py-24 md:py-32">
        <div className="absolute inset-0 opacity-10">
          <svg className="absolute bottom-0 left-0 w-full h-24" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="white" opacity="0.3"></path>
          </svg>
        </div>
        <div className="container mx-auto px-6 text-center relative z-10">
          <h1 className="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-tight mb-6 animate-fade-in">
            Créez un site web professionnel{" "}
            <span className="block text-yellow-300 drop-shadow-lg">en 60 secondes</span>
            <span className="text-2xl md:text-3xl block mt-2">… sans écrire une ligne de code.</span>
          </h1>
          <p className="text-lg md:text-xl lg:text-2xl max-w-3xl mx-auto mb-8 text-orange-100">
            BuildAI-Ex utilise l’intelligence artificielle pour générer, personnaliser et déployer votre site.
            <strong className="block mt-2 text-white">Essayez gratuitement, aucune carte bancaire requise.</strong>
          </p>
          <a href="#generator" className="inline-block bg-white text-orange-600 px-8 py-4 rounded-full font-bold text-lg shadow-lg hover:bg-gray-100 hover:scale-105 transition-transform duration-200">
            ✨ Générez votre site maintenant – c’est gratuit
          </a>
          <div className="mt-8 flex justify-center gap-4 text-sm text-orange-200">
            <span>✓ Aucune compétence technique</span>
            <span>✓ Site responsive</span>
            <span>✓ Déploiement 1 clic</span>
          </div>
        </div>
      </section>

      {/* Features (orange clair) */}
      <section id="features" className="py-20 bg-white">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">
            Pourquoi choisir BuildAI-Ex ?
          </h2>
          <div className="grid md:grid-cols-3 gap-8">
            <div className="bg-orange-50 p-6 rounded-xl shadow-md hover:shadow-lg transition text-center">
              <i className="fas fa-robot text-5xl text-orange-600 mb-4"></i>
              <h3 className="text-xl font-semibold mb-2">IA générative</h3>
              <p className="text-gray-600">Contenu, design et images adaptés à votre secteur.</p>
            </div>
            <div className="bg-orange-50 p-6 rounded-xl shadow-md hover:shadow-lg transition text-center">
              <i className="fas fa-drag-drop text-5xl text-orange-600 mb-4"></i>
              <h3 className="text-xl font-semibold mb-2">Éditeur visuel</h3>
              <p className="text-gray-600">Personnalisez votre site par glisser-déposer.</p>
            </div>
            <div className="bg-orange-50 p-6 rounded-xl shadow-md hover:shadow-lg transition text-center">
              <i className="fas fa-cloud-upload-alt text-5xl text-orange-600 mb-4"></i>
              <h3 className="text-xl font-semibold mb-2">Déploiement 1 clic</h3>
              <p className="text-gray-600">Mettez en ligne sur Vercel ou exportez votre site.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Générateur (orange accent) */}
      <section id="generator" className="py-20 bg-gray-100">
        <div className="container mx-auto px-6 max-w-4xl">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-6">Générez votre site maintenant</h2>
          <p className="text-center text-gray-600 mb-8">Remplissez le formulaire ci-dessous, notre IA crée un site unique pour vous.</p>
          <div className="bg-white rounded-xl shadow-lg p-8">
            <form onSubmit={(e) => { e.preventDefault(); generate(); }}>
              <div className="mb-4">
                <label className="block text-gray-700 font-medium mb-2">Type de profil</label>
                <select value={profile} onChange={(e) => setProfile(e.target.value)} className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                  <option value="autoentrepreneur">Auto-entrepreneur</option>
                  <option value="pme">PME / Entreprise</option>
                  <option value="freelance">Freelance</option>
                  <option value="particulier">Particulier / Portfolio</option>
                </select>
              </div>
              <div className="mb-4">
                <label className="block text-gray-700 font-medium mb-2">Description de votre projet</label>
                <textarea value={description} onChange={(e) => setDescription(e.target.value)} rows={4} className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Ex: Salon de coiffure éco-responsable avec prise de rendez-vous en ligne"></textarea>
              </div>
              <button type="submit" disabled={loading} className="w-full bg-orange-600 text-white py-3 rounded-lg font-semibold hover:bg-orange-700 transition disabled:opacity-50">
                {loading ? 'Génération en cours...' : '🚀 Générer mon site'}
              </button>
            </form>
          </div>
        </div>
      </section>

      {/* Éditeur après génération */}
      {showEditor && siteHtml && (
        <section id="editor-section" className="py-20 bg-white">
          <div className="container mx-auto px-6">
            <h2 className="text-3xl font-bold text-center text-gray-800 mb-6">Personnalisez votre site</h2>
            <Editor initialHtml={siteHtml} onHtmlChange={setSiteHtml} />
            <div className="flex justify-center gap-4 mt-8">
              <button onClick={() => { const blob = new Blob([siteHtml], { type: 'text/html' }); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = 'buildai-ex-site.html'; a.click(); }} className="bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700">📥 Exporter HTML</button>
              <button onClick={async () => { const res = await fetch('/api/deploy', { method: 'POST', body: JSON.stringify({ htmlContent: siteHtml, projectName: `buildai-${Date.now()}` }) }); const data = await res.json(); alert(data.url ? `Déployé sur : https://${data.url}` : 'Erreur déploiement'); }} className="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700">🌍 Déployer sur Vercel</button>
            </div>
          </div>
        </section>
      )}

      {/* Pricing (orange et ambre) */}
      <section id="pricing" className="py-20 bg-gradient-to-br from-orange-50 to-amber-50">
        <div className="container mx-auto px-6">
          <h2 className="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">Tarifs transparents</h2>
          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div className="border rounded-xl p-6 text-center shadow-md bg-white">
              <h3 className="text-2xl font-bold">Gratuit</h3>
              <p className="text-gray-500 mt-2">Pour tester</p>
              <p className="text-4xl font-bold mt-4">0€<span className="text-lg font-normal">/mois</span></p>
              <ul className="mt-6 space-y-2 text-gray-600">
                <li>✓ 1 site</li>
                <li>✓ 3 générations / mois</li>
                <li>✓ Export HTML</li>
              </ul>
              <a href="#generator" className="block mt-8 bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300 transition">Commencer</a>
            </div>
            <div className="border-2 border-orange-600 rounded-xl p-6 text-center shadow-lg bg-white transform scale-105">
              <h3 className="text-2xl font-bold text-orange-600">Pro</h3>
              <p className="text-gray-500 mt-2">Pour les pros</p>
              <p className="text-4xl font-bold mt-4">29€<span className="text-lg font-normal">/mois</span></p>
              <ul className="mt-6 space-y-2 text-gray-600">
                <li>✓ Sites illimités</li>
                <li>✓ Générations illimitées</li>
                <li>✓ Images IA haute qualité</li>
                <li>✓ Déploiement automatisé</li>
                <li>✓ Support prioritaire</li>
              </ul>
              <a href="#" className="block mt-8 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition">Choisir Pro</a>
            </div>
            <div className="border rounded-xl p-6 text-center shadow-md bg-white">
              <h3 className="text-2xl font-bold">Enterprise</h3>
              <p className="text-gray-500 mt-2">Sur mesure</p>
              <p className="text-4xl font-bold mt-4">Sur devis</p>
              <ul className="mt-6 space-y-2 text-gray-600">
                <li>✓ Modèles personnalisés</li>
                <li>✓ API dédiée</li>
                <li>✓ Hébergement privé</li>
                <li>✓ SLA garanti</li>
              </ul>
              <a href="#contact" className="block mt-8 bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300 transition">Nous contacter</a>
            </div>
          </div>
        </div>
      </section>

      {/* Contact (orange) */}
      <section id="contact" className="py-20 bg-white">
        <div className="container mx-auto px-6 max-w-2xl">
          <h2 className="text-3xl font-bold text-center text-gray-800 mb-6">Contactez-nous</h2>
          <form action="/api/contact" method="POST" className="bg-gray-50 p-8 rounded-xl shadow-md">
            <div className="mb-4">
              <input type="text" name="name" placeholder="Votre nom" className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500" required />
            </div>
            <div className="mb-4">
              <input type="email" name="email" placeholder="Email" className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500" required />
            </div>
            <div className="mb-4">
              <textarea name="message" rows={4} placeholder="Message" className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500" required></textarea>
            </div>
            <button type="submit" className="w-full bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition">Envoyer</button>
          </form>
        </div>
      </section>

      {/* Footer (orange foncé) */}
      <footer className="bg-orange-900 text-white py-8">
        <div className="container mx-auto px-6 text-center">
          <p>&copy; 2025 BuildAI-Ex. Tous droits réservés.</p>
          <div className="flex justify-center space-x-4 mt-4">
            <a href="#" className="hover:text-orange-300"><i className="fab fa-twitter"></i></a>
            <a href="#" className="hover:text-orange-300"><i className="fab fa-linkedin"></i></a>
            <a href="#" className="hover:text-orange-300"><i className="fab fa-github"></i></a>
          </div>
        </div>
      </footer>

      <style jsx global>{`
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
          animation: fadeIn 0.8s ease-out forwards;
        }
      `}</style>
    </div>
  );
}