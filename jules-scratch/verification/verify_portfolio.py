import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # 1. Go to the local website
    # IMPORTANT: Use the correct URL for your local environment.
    # Since we copied files to /var/www/html, the root URL should work.
    page.goto("http://localhost:80")

    # 2. Take a screenshot of the desktop view
    page.wait_for_load_state('networkidle')
    page.screenshot(path="jules-scratch/verification/01_desktop_view.png")

    # 3. Switch to mobile viewport
    page.set_viewport_size({"width": 375, "height": 667})

    # 4. Open the navigation drawer
    menu_toggle = page.locator("#menu-toggle")
    menu_toggle.click()

    # Wait for the sidebar to be visible
    sidebar = page.locator("#sidebar")
    expect(sidebar).to_be_visible()
    page.wait_for_timeout(500) # Wait for animation to complete

    # 5. Take a screenshot of the mobile view with the drawer open
    page.screenshot(path="jules-scratch/verification/02_mobile_drawer.png")

    # 6. Switch to dark mode
    theme_toggle = page.locator("#theme-toggle")
    theme_toggle.click()

    # Wait for the body to have the 'dark-mode' class
    body = page.locator("body")
    expect(body).to_have_class(re.compile(r'dark-mode'))
    page.wait_for_timeout(500) # Wait for animation to complete

    # 7. Take a screenshot of the dark mode view
    page.screenshot(path="jules-scratch/verification/03_dark_mode.png")

    print("Screenshots taken successfully.")

    # ---------------------
    context.close()
    browser.close()

with sync_playwright() as playwright:
    run(playwright)