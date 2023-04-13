# DRM-X 4.0 WooCommerce (WordPress) Plugin -- (drmx-integration-woocommerce)

DRM-X 4.0 supports [encrypted video](https://www.haihaisoft.com/Video-DRM-Protection.aspx), audio and PDF. DRM-X 4.0 protected courses can [prevent screen recording](https://www.haihaisoft.com/Smart-Prevent-Screen-Recording.aspx), display user information [watermark](https://www.drm-x.com/Secure-Architecture-4.0.aspx#watermark-scroll-tab), and bind user devices to [prevent sharing accounts](https://www.drm-x.com/Secure-Architecture-4.0.aspx#binding-scroll-tab). Through this plugin, you can easily integrate DRM-X 4.0 system with your WooCommerce system seamlessly. Protects WordPress courses from piracy and prevents WooCommerce products from recording.

**[Download DRM-X 4.0 WooCommerce Integration Plugin](https://www.drm-x.com/download/DRM-X_4.0_Integration/drmx-integration-woocommerce.zip)**

# File Description

### DRM-X 4.0 Integration Core Files

- **drmx_index.php**  DRM-X 4.0 integrated index file, it will get DRM-X 4.0 encryption parameters.
- **drmx_login.php** Verify user login information and course orders, call DRM-X 4.0 integration interface to add users, update Rights, and get licenses.

- **drmx_licError.php** Show the error encountered when obtaining a license.

- **licstore.php** Storage of acquired licenses.
- **includes/drm_nusoap.php** Third-party nusoap class file to call DRM-X 4.0 XML Web Service interface.

- **public/css/login-style.css** DRM-X 4.0 Integration Style files for pages.
- **public/images/**  DRM-X 4.0 Integration Get license page image folder.

### Plug-in core files

- **drmx-integration-woocommerce.php** Plug-in registration file.
- **drmx-integration-woocommerce -settings.php** Plug-in settings page.



# How to use the plugin?

## 1. Set DRM-X 4.0 parameters in the plugin

### 	1. Install and activate the plugin

### 	2. Visit the plugin settings page

### 	3. Set the DRM-X 4.0 parameters

#### 	DRM-X 4.0 parameter description:

- **DRM-X 4.0 Account:** The email address you entered when you registered your DRM-X 4.0 account.
- **DRM-X 4.0 Web Service Authentication String:** Visit the [DRM-X 4.0 website integration settings](http://4.drm-x.com/SetIntegration.aspx), Select custom login page integration and set a Web Service Authentication String for DRM-X web service. Then enter the same Authentication String in the drmx-integration-woocommerce plugin settings page.
- **DRM-X 4.0 XML Web Service URL:** DRM-X 4.0 system XML Web Service interface URL.
- **DRM-X 4.0 GroupID:** The User Group ID of your DRM-X 4.0 account, please login to your DRM-X 4.0 account and visit the User Group page to check it. You only need to create one user group, please do not delete the user group in your DRM-X 4.0 account after setting.
- **DRM-X 4.0 RightsID:** The Rights ID of your DRM-X 4.0 account, please login to your DRM-X 4.0 account and visit the Rights page to check it. You only need to create one Rights, please do not delete the rights in your DRM-X 4.0 account after setting, users will automatically update the Rights when they get the license, and get the updated Rights.
- **User Bind Count:** Hardware binding restrictions for student login accounts. The user binding count is only valid for users who have not yet acquired a license. If you want to modify the user who has already acquired the license, please login your DRM-X 4.0 account, edit the user, reset the user binding count.

## 2. Associate WooCommerce Products with DRM-X 4.0 License Profile

### 1. Create and get WooCommerce Product ID 

Create a product, then look at the Products list, and get the product ID under the product name.

### 2. Create the corresponding DRM-X 4.0 License Profile for the product 

Login to your DRM-X 4.0 account, visit the License Profile page, and create a License Profile that belongs to the course.

- **Profile Name:** We recommend that the License Profile name be the same as the course name.
- **Default Rights:** Select any one of the Rights.
- **ProductID in your system(Option):** Set the ID of the corresponding product here, which is the post id viewed in the first step.

Finally, select License is Revocable below and click the Submit button.

### 3. Protect files

Visit the DRM-X 4.0 Protect Files page, select the License Profile (product) of the file you want to protect, and complete the protection.

[**How to protect videos and PDF files?**](https://www.drm-x.com/DRM-X4.0_encryption_tutorial.aspx)

### 4. Upload the protected file

​	Upload the protected file to your Wordpress web server, or to a separate file storage server. And modify the WooCommerce product to make it a downloadable virtual product.

### 5. Open protected files with Xvast browser -- End User

When the user purchases the protected product, the user must use the Xvast browser to open the protected file. [https://www.xvast.com](https://www.xvast.com)

## 3. Set the URL of DRM-X 4.0 obtain license

Visit DRM-X 4.0 "**Account Settings**" - "**Site Integration Preferences**" - select "**Custom Login Page Integration**"

Set the **License URL**: The URL starts with the domain name of your WordPress site, for example: https://www.domain.com/wp-content/plugins/drmx-integration-woocommerce/drmx_index.php You can also check the DRM-X 4.0 with WooCommerce plugin page for a line of blue URL, which is the address of your website to get the license.



**If you have any questions about the integration, you can feel free to contact us.**

[Haihaisoft (DRM Provider) official website](https://www.haihaisoft.com): [https://www.haihaisoft.com](https://www.haihaisoft.com) 

[DRM-X Digital Rights Management Platform](https://www.drm-x.com): [https://www.drm-x.com](https://www.drm-x.com) 

[DRM-X Free Trial](https://www.drm-x.com/Fees-Compare-4.0.aspx): [https://www.drm-x.com/Fees-Compare-4.0.aspx](https://www.drm-x.com/Fees-Compare-4.0.aspx)
