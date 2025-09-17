// Composant React simple pour WordPress - compatible avec CDN
const BeefChart = () => {
  const [data, setData] = React.useState([]);
  const [svgLoaded, setSvgLoaded] = React.useState(false);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    // Charger les données depuis WordPress
    loadBeefData();

    // Charger le SVG
    loadSVG();
  }, []);

  const loadBeefData = () => {
    // Utiliser les données passées par PHP au lieu d'AJAX
    if (typeof window.beefChartData !== "undefined") {
      const formattedData = window.beefChartData.map((item) => ({
        id: item.id,
        name: item.name,
        price: parseFloat(item.price),
        available: item.available == "1",
      }));
      setData(formattedData);
      setLoading(false);
    } else {
      // Fallback avec données par défaut
      setData([
        { name: "Queue", price: 15, available: true },
        { name: "Langue", price: 35, available: true },
        { name: "Plat de joue", price: 15, available: true },
        { name: "Gros bout de poitrine", price: 25, available: true },
        { name: "Jumeau à pot-au-feu", price: 45, available: true },
      ]);
      setLoading(false);
    }
  };

  const loadSVG = () => {
    // Charger le SVG depuis le bon chemin
    fetch("/wp-content/plugins/beef-chart-plugin/assets/Beef_cuts_France.svg")
      .then((res) => res.text())
      .then((svg) => {
        echarts.registerMap("Beef_cuts_France", { svg });
        setSvgLoaded(true);
      })
      .catch((error) => {
        console.log("SVG non trouvé, utilisation de données de test");
        setSvgLoaded(true);
      });
  };

  const chartData = data
    .filter((item) => item.available)
    .map((item) => ({ name: item.name, value: item.price }));

  const option = {
    tooltip: {
      trigger: "item",
      formatter: "{b}: {c}€",
    },
    visualMap: {
      left: "center",
      bottom: "10%",
      min: 5,
      max: 100,
      orient: "horizontal",
      text: ["", "Prix (€)"],
      realtime: true,
      calculable: true,
      inRange: {
        color: ["#dbac00", "#db6e00", "#cf0000"],
      },
    },
    series: [
      {
        name: "Coupes de Bœuf Françaises",
        type: "map",
        map: "Beef_cuts_France",
        roam: true,
        emphasis: {
          label: {
            show: false,
          },
        },
        selectedMode: false,
        data: chartData,
      },
    ],
  };

  if (loading) {
    return React.createElement(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "400px",
          fontSize: "20px",
        },
      },
      "Chargement..."
    );
  }

  // Utiliser echarts directement au lieu de ReactECharts
  return React.createElement(
    "div",
    {
      style: {
        maxWidth: "1200px",
        margin: "0 auto",
        padding: "20px",
        backgroundColor: "#EDF1FF",
        minHeight: "100vh",
      },
    },
    React.createElement(
      "div",
      { style: { display: "flex", flexWrap: "wrap", gap: "20px" } },
      [
        React.createElement(
          "div",
          { key: "info", style: { flex: "1 1 500px" } },
          [
            React.createElement(
              "div",
              {
                style: {
                  background: "white",
                  padding: "20px",
                  borderRadius: "8px",
                  boxShadow: "0 2px 10px rgba(0,0,0,0.1)",
                  marginBottom: "20px",
                },
              },
              [
                React.createElement(
                  "p",
                  { style: { margin: "10px 0", lineHeight: "1.6" } },
                  "Ce plugin interactif de visualisation des coupes de bœuf françaises est conçu spécifiquement pour les sites web des bouchers. Il permet aux clients de découvrir les différentes pièces de viande avec leurs prix en temps réel, facilitant ainsi la prise de commande et l'expérience utilisateur."
                ),
                React.createElement(
                  "p",
                  { style: { margin: "10px 0", lineHeight: "1.6" } },
                  "Développé entièrement en interne par notre équipe de développement, ce plugin représente une opportunité commerciale unique. Nous pouvons le commercialiser sur le marché WordPress, générant des revenus récurrents pour l'agence."
                ),
                React.createElement(
                  "div",
                  {
                    style: {
                      background: "#f9f9f9",
                      padding: "15px",
                      borderRadius: "5px",
                      marginTop: "15px",
                    },
                  },
                  [
                    React.createElement(
                      "h4",
                      { style: { margin: "0 0 10px 0" } },
                      "Avantages :"
                    ),
                    React.createElement(
                      "ul",
                      { style: { margin: "0", paddingLeft: "20px" } },
                      [
                        "Interface interactive et moderne",
                        "Données mises à jour en temps réel",
                        "Facile à intégrer sur tout site WordPress",
                        "Personnalisable selon les besoins du client",
                        "Support technique inclus",
                        "Optimisé pour le référencement",
                        "Compatible mobile et desktop",
                      ].map((advantage, index) =>
                        React.createElement("li", { key: index }, advantage)
                      )
                    ),
                  ]
                ),
              ]
            ),
          ]
        ),
        React.createElement(
          "div",
          { key: "chart", style: { flex: "1 1 500px" } },
          [
            React.createElement(
              "div",
              {
                style: {
                  background: "white",
                  padding: "20px",
                  borderRadius: "8px",
                  boxShadow: "0 2px 10px rgba(0,0,0,0.1)",
                },
              },
              [
                React.createElement(
                  "h3",
                  { style: { margin: "0 0 20px 0" } },
                  "Carte Interactive des Coupes de Bœuf"
                ),
                React.createElement("div", {
                  id: "beef-chart-container",
                  style: { height: "600px", width: "100%" },
                }),
              ]
            ),
          ]
        ),
      ]
    )
  );
};

// Fonction pour rendre le composant et initialiser le graphique
function renderBeefChart(elementId) {
  const element = document.getElementById(elementId);
  if (element) {
    const root = ReactDOM.createRoot(element);
    root.render(React.createElement(BeefChart));

    // Initialiser ECharts après le rendu React
    setTimeout(() => {
      const chartContainer = document.getElementById("beef-chart-container");
      if (chartContainer && typeof echarts !== "undefined") {
        const chart = echarts.init(chartContainer);

        // Charger les données et configurer le graphique
        if (typeof window.beefChartData !== "undefined") {
          const data = window.beefChartData.map((item) => ({
            id: item.id,
            name: item.name,
            price: parseFloat(item.price),
            available: item.available == "1",
          }));

          const chartData = data
            .filter((item) => item.available)
            .map((item) => ({ name: item.name, value: item.price }));

          const option = {
            tooltip: {
              trigger: "item",
              formatter: "{b}: {c}€",
            },
            visualMap: {
              left: "center",
              bottom: "10%",
              min: 5,
              max: 100,
              orient: "horizontal",
              text: ["", "Prix (€)"],
              realtime: true,
              calculable: true,
              inRange: {
                color: ["#dbac00", "#db6e00", "#cf0000"],
              },
            },
            series: [
              {
                name: "Coupes de Bœuf Françaises",
                type: "map",
                map: "Beef_cuts_France",
                roam: true,
                emphasis: {
                  label: {
                    show: false,
                  },
                },
                selectedMode: false,
                data: chartData,
              },
            ],
          };

          chart.setOption(option);

          // Charger le SVG
          fetch(
            "/wp-content/plugins/beef-chart-plugin/assets/Beef_cuts_France.svg"
          )
            .then((res) => res.text())
            .then((svg) => {
              echarts.registerMap("Beef_cuts_France", { svg });
              chart.setOption(option); // Re-render avec la carte
            })
            .catch((error) => {
              console.log("SVG non trouvé, utilisation de données de test");
            });
        }
      }
    }, 100);
  }
}
