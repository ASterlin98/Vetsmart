import time
from playwright.sync_api import sync_playwright

def run(playwright):
    time.sleep(5) # Wait for server to start
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in as superadmin
    page.goto("http://127.0.0.1:8080/vetsmart/login")
    page.wait_for_load_state("networkidle")
    page.fill('input[name="email"]', "andres.rojast98@gmail.com")
    page.fill('input[name="password"]', "12345")
    page.click('button[type="submit"]')
    page.wait_for_load_state("networkidle")

    # Go to dashboard and take screenshot
    page.goto("http://127.0.0.1:8080/vetsmart/super_admin/dashboard")
    page.wait_for_load_state("networkidle")
    page.screenshot(path="jules-scratch/verification/dashboard.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)