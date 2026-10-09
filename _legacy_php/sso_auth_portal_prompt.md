# SSO Auth Portal Update Prompt

**Instructions:** Copy and paste the following prompt into your Cursor, Lovable, or v0 terminal for your running **Accounts/Auth App** (the app running on `accounts.ericksonholding.com` or `localhost:3001`):

---

## **PROMPT START**

I am currently building my single sign-on Authentication Portal with Firebase, but I am running into an issue when external apps (like my local testing app on `http://localhost`) attempt to use the SSO Popup flow. It is returning an **"Unauthorized SSO Origin"** error. 

Right now, my SSO flow doesn't have a secure way to know which third-party apps are actually allowed to request logins. 

I need you to build a dynamic **API Token Application Registry** directly into this Auth Portal to solve this.

Please implement the following architectural features:

### 1. SSO Connected Apps Database
Create a database table/collection (e.g., `sso_clients` in Firebase Firestore or your Postgres database) to store registered applications. 
It should contain:
- `client_id` or `api_token` (A generated API Key)
- `app_name` (e.g., "SVI Platform")
- `allowed_origins` (An array of valid URLs, e.g., `["http://localhost", "https://svi.ericksonholding.com"]`)

### 2. Update the Login Route (`/login`)
When a client application opens the popup, they will now pass an API token in the URL query string, like this:
`http://localhost:3001/login?isSSO=true&token=svi_secret_key_123`

Update the `/login` page logic to:
- Read the `token` from the URL parameters upon loading the popup.
- Fetch the token from the `sso_clients` database.
- If the token is invalid or missing, immediately show an error: *"Invalid SSO Application Token"*.
- If the token is valid, verify that the current `returnOrigin` (or the `document.referrer`) perfectly matches one of the domains listed inside the `allowed_origins` array.
- If the origin does not match the token's allowed list, block the login with the error: *"Unauthorized SSO Origin: [Origin] is not approved for this API Token."*

### 3. Secure the `postMessage` Dispatch
In my existing `handleLoginSuccess` function, I am broadcasting the Firebase user payload back to `window.opener`. 

Update this function so that it no longer uses a hardcoded origin or '*' when sending the `postMessage`. It should explicitly specify the validated origin we checked in Step 2:

```javascript
// Example modification:
if (window.opener && validatedAppOrigin) {
  console.log("SSO: Broadcasting success to origin:", validatedAppOrigin);
  window.opener.postMessage(
    {
      type: "SSO_AUTH_SUCCESS",
      payload: {
        uid: userCredential.user.uid,
        email: userCredential.user.email,
        displayName: userCredential.user.displayName,
        photoURL: userCredential.user.photoURL,
        idToken: await userCredential.user.getIdToken()
      }
    },
    validatedAppOrigin // IMPORTANT: Use the exact origin we validated, not '*' or a hardcoded string
  );
  
  // Give the message a few ms to traverse before closing
  setTimeout(() => {
    window.close();
  }, 200);
}
```

Please generate the code required to create this API Token registry, query the allowed origins during the login popup lifecycle, and securely block any unauthorized origin requests!

---

## **PROMPT END**
