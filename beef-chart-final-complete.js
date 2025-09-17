// Beef Chart Component - Version simplifiée et robuste
(function () {
  "use strict";

  console.log("Beef Chart Plugin - Starting...");

  window.renderBeefChart = function (containerId) {
    console.log("Rendering beef chart in:", containerId);

    const container = document.getElementById(containerId);
    if (!container) {
      console.error("Container not found:", containerId);
      return;
    }

    // Style de base
    container.style.padding = "20px";
    container.style.border = "1px solid #ddd";
    container.style.borderRadius = "8px";
    container.style.backgroundColor = "#fafafa";
    container.style.overflow = "hidden";
    container.style.position = "relative";

    // Contenu initial simple
    container.innerHTML = `
            <h3 style="color: #2e7d32; margin-top: 0;">🐄 Carte des Coupes de Bœuf</h3>
            <div id="loading-${containerId}" style="text-align: center; padding: 20px; color: #666;">
                Chargement de la carte...
            </div>
            <div id="chart-${containerId}" style="width: 100%; height: 400px; display: none; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"></div>
            <div id="error-${containerId}" style="display: none; color: #f44336; text-align: center; padding: 20px;"></div>
        `;

    const loadingDiv = document.getElementById(`loading-${containerId}`);
    const chartDiv = document.getElementById(`chart-${containerId}`);
    const errorDiv = document.getElementById(`error-${containerId}`);

    // Vérifier si ECharts est disponible
    if (typeof echarts === "undefined") {
      loadingDiv.style.display = "none";
      errorDiv.style.display = "block";
      errorDiv.innerHTML =
        "<p>❌ Erreur: ECharts n'est pas chargé. Vérifiez votre connexion internet.</p>";
      return;
    }

    let myChart = null;

    // Fonction pour créer la carte SVG des coupes de bœuf
    function createBeefChart(data) {
      try {
        console.log("Creating beef chart with data:", data);

        if (myChart) {
          myChart.dispose();
        }

        chartDiv.innerHTML = "";
        myChart = echarts.init(chartDiv);

        // Fetch the correct SVG from plugin root
        const svgUrl = window.beefChartAjax.plugin_url + "Beef_cuts_France.svg";
        console.log("Fetching SVG from:", svgUrl);

        fetch(svgUrl)
          .then((response) => {
            console.log("SVG fetch response status:", response.status);
            if (!response.ok) {
              throw new Error("HTTP error! status: " + response.status);
            }
            return response.text();
          })
          .then((svg) => {
            console.log("SVG content length:", svg.length);
            console.log("SVG content preview:", svg.substring(0, 200) + "...");

            try {
              echarts.registerMap("Beef_cuts_France", { svg: svg });
              console.log("SVG registered successfully");
            } catch (registerError) {
              console.error("Error registering SVG map:", registerError);
              throw new Error(
                "Failed to register SVG map: " + registerError.message
              );
            }

            const option = {
              tooltip: {
                trigger: "item",
                formatter: function (params) {
                  return params.name + "<br/>Prix: " + params.value + "€/kg";
                },
              },
              visualMap: {
                left: "center",
                bottom: "10%",
                min: 5,
                max: 100,
                orient: "horizontal",
                text: ["", "Prix"],
                realtime: true,
                calculable: true,
                inRange: {
                  color: ["#dbac00", "#db6e00", "#cf0000"],
                },
              },
              series: [
                {
                  name: "French Beef Cuts",
                  type: "map",
                  map: "Beef_cuts_France",
                  roam: true,
                  emphasis: {
                    label: {
                      show: false,
                    },
                  },
                  selectedMode: false,
                  data: data,
                },
              ],
            };

            myChart.setOption(option);
            console.log("Beef chart created successfully");
          })
          .catch((error) => {
            console.error("Error loading SVG:", error);
            loadingDiv.style.display = "none";
            errorDiv.style.display = "block";
            errorDiv.innerHTML =
              "<p>❌ Erreur lors du chargement du SVG: " +
              error.message +
              "</p>";
          });
      } catch (error) {
        console.error("Error creating beef chart:", error);
        loadingDiv.style.display = "none";
        errorDiv.style.display = "block";
        errorDiv.innerHTML =
          "<p>❌ Erreur lors de la création de la carte: " +
          error.message +
          "</p>";
      }
    }

    // Charger les données WordPress
    if (typeof window.beefChartAjax !== "undefined") {
      console.log("Loading data from WordPress...");

      fetch(window.beefChartAjax.ajax_url, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "Cache-Control": "no-cache",
        },
  body: "action=yfbcc_get_beef_data&nonce=" + window.beefChartAjax.nonce,
      })
        .then((response) => {
          console.log("Response received:", response.status);
          if (!response.ok) {
            throw new Error("HTTP error! status: " + response.status);
          }
          return response.json();
        })
        .then((result) => {
          console.log("Data loaded:", result);

          loadingDiv.style.display = "none";

          if (result.success && result.data && result.data.length > 0) {
            // Convertir les données pour ECharts
            const chartData = result.data
              .filter((item) => item.available == 1 || item.available === true)
              .map((item) => ({
                name: item.name,
                value: parseFloat(item.price) || 0,
              }));

            console.log("Filtered data for chart:", chartData);

            if (chartData.length > 0) {
              chartDiv.style.display = "block";
              createBeefChart(chartData);
            } else {
              errorDiv.style.display = "block";
              errorDiv.innerHTML = "<p>⚠️ Aucune coupe de bœuf disponible</p>";
            }
          } else {
            errorDiv.style.display = "block";
            errorDiv.innerHTML =
              "<p>⚠️ Aucune donnée trouvée dans la base de données</p>";
          }
        })
        .catch((error) => {
          console.error("Error loading data:", error);
          loadingDiv.style.display = "none";
          errorDiv.style.display = "block";
          errorDiv.innerHTML =
            "<p>❌ Erreur de chargement des données: " + error.message + "</p>";
        });
    } else {
      console.error("beefChartAjax not defined");
      loadingDiv.style.display = "none";
      errorDiv.style.display = "block";
      errorDiv.innerHTML = "<p>❌ Configuration AJAX manquante</p>";
    }

    // Gérer le redimensionnement
    window.addEventListener("resize", function () {
      if (myChart) {
        try {
          myChart.resize();
        } catch (e) {
          console.warn("Error resizing chart:", e);
        }
      }
    });

    // Gérer les changements d'orientation sur mobile
    window.addEventListener("orientationchange", function () {
      setTimeout(function () {
        if (myChart) {
          try {
            myChart.resize();
          } catch (e) {
            console.warn("Error resizing chart on orientation change:", e);
          }
        }
      }, 500);
    });
  };

  console.log("Beef Chart Plugin - Ready");
})();
