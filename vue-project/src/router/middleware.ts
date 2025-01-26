import { useLicense } from "@/pages/config/license/UseLicense";

export default function (router) {
  const { licenseKey, isValidLicenseKey, loadLicenseKey } = useLicense(false);

  router.beforeEach(async (to, from, next) => {
    try {
      // Load the license key if not already loaded
      if (!licenseKey.value) {
        await loadLicenseKey();
      }

      // Allow access to the license route without validation
      if (to.name === "license") {
        return next();
      }

      // Redirect to the license route if the key is invalid or missing
      if (!isValidLicenseKey.value) {
        return next({ name: "license" });
      }

      // Proceed to the intended route
      next();
    } catch (error) {
      console.error("Error validating license:", error);
      // Redirect to the license page in case of an error
      next({ name: "license" });
    }
  });

  return router;
}
