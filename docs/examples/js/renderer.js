/**
 * Clustering Markers
 * https://plugins.doublesecretagency.com/google-maps/dynamic-maps/clustering-markers/
 *
 * If you'd like to use custom clustering icons,
 * copy this script and adapt it to your needs.
 *
 * You may rename the object, and/or create different
 * kinds of cluster renderers. It's possible to use
 * different renderers with different maps.
 *
 * Keep in mind, each cluster icon is effectively
 * the same type of object as a normal Marker.
 */

// Custom rendering object
const MyCustomRenderer = {
    'render': function ({ count, position }, stats) {

        // Whether this cluster has more markers than average, or at least 10
        const aboveAverage = count > Math.max(10, stats.clusters.markers.mean);

        // Change color based on general marker count
        const color = aboveAverage ? '#ff0000' : '#0000ff';

        // Create an SVG with dynamic fill color
        const svg = window.btoa(`
  <svg fill="${color}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240">
    <circle cx="120" cy="120" opacity=".6" r="70" />
    <circle cx="120" cy="120" opacity=".3" r="90" />
    <circle cx="120" cy="120" opacity=".2" r="110" />
  </svg>`);

        // Create the cluster marker using an SVG icon
        return new google.maps.Marker({
            position,
            icon: {
                url: `data:image/svg+xml;base64,${svg}`,
                scaledSize: new google.maps.Size(45, 45)
            },
            label: {
                text: String(count),
                color: 'rgba(255,255,255,0.9)',
                fontSize: '12px'
            },
            // Adjust the zIndex to be above other markers
            zIndex: Number(google.maps.Marker.MAX_ZINDEX) + count
        });
    }
};
