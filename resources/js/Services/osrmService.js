export async function fetchRoute(origin, destination) {
    const url = `https://router.project-osrm.org/route/v1/driving/${origin.lng},${origin.lat};${destination.lng},${destination.lat}?overview=full&geometries=geojson`;
    const res = await fetch(url);
    if (!res.ok) throw new Error("OSRM request failed");
    const data = await res.json();
    if (!data.routes || !data.routes.length) throw new Error("Route not found");
    return data.routes[0];
}

export async function fetchOptimizedTrip(koordinatString) {
    const url = `https://router.project-osrm.org/trip/v1/driving/${koordinatString}?roundtrip=false&source=first&destination=last&overview=full&geometries=geojson`;
    const res = await fetch(url);
    const data = await res.json();
    if (data.code !== "Ok") throw new Error(data.message || "OSRM Trip Error");
    return data;
}
