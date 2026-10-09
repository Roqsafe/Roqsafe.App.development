/**
 * Erickson Holding SSO Client Integration (Vanilla JS)
 */
const EH_AUTH_URL = "http://localhost:3001"; // Or https://accounts.ericksonholding.com
const THIS_ORIGIN = window.location.origin;

function triggerEricksonSSO(options) {
  const onSuccess = options.onSuccess;
  const onError = options.onError;
  const apiToken = options.apiToken || ''; // Optional API Key
  
  const encodedOrigin = encodeURIComponent(THIS_ORIGIN);
  let ssoUrl = EH_AUTH_URL + "/login?isSSO=true&returnOrigin=" + encodedOrigin;
  if (apiToken) ssoUrl += "&token=" + encodeURIComponent(apiToken);

  const popup = window.open(
    ssoUrl,
    "EricksonHoldingSSO",
    "width=480,height=640,top=100,left=100,scrollbars=yes"
  );

  if (!popup) {
    if (onError) onError("Popup was blocked. Please allow popups for this site.");
    return;
  }

  const handleMessage = function(event) {
    console.log("[SSO] Message received from origin:", event.origin);
    
    // SECURITY: Only accept messages from the trusted auth portal
    if (EH_AUTH_URL.includes("localhost") && event.origin.includes("localhost")) {
        // Dev mode allows cross localhost ports
    } else if (event.origin !== EH_AUTH_URL) {
        console.warn("[SSO] Unauthorized Origin Rejected:", event.origin);
        return;
    }

    if (event.data && event.data.type === "SSO_AUTH_SUCCESS") {
      console.log("[SSO] Authentication Success Payload:", event.data.payload);
      window.removeEventListener("message", handleMessage);
      const payload = event.data.payload;
      sessionStorage.setItem("eh_user", JSON.stringify(payload));
      if (onSuccess) onSuccess(payload);
    } else {
      console.log("[SSO] Unrecognized message type:", event.data?.type);
    }
  };

  window.addEventListener("message", handleMessage);

  const pollTimer = setInterval(function() {
    if (popup.closed) {
      clearInterval(pollTimer);
      console.log("[SSO] Popup detected as closed. Removing listener in 1000ms...");
      // Delay removal to allow pending message events to process
      setTimeout(() => {
        window.removeEventListener("message", handleMessage);
      }, 1000);
      
      // We don't call error immediately because it might have succeeded just before closing
    }
  }, 500);
}
