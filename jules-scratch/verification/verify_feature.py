import time
from playwright.sync_api import sync_playwright

def run(playwright):
    time.sleep(5) # Wait for server to start
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    # Log in as admin
    page.goto("http://127.0.0.1:9000/login")
    page.wait_for_load_state("networkidle")
    page.fill('input[name="email"]', "andres@prueba.com")
    page.fill('input[name="password"]', "12345")
    page.click('button[type="submit"]')

    # Navigate to reports page
    page.goto("http://127.0.0.1:9000/admin/reportes")
    page.wait_for_load_state("networkidle")

    # Click the "Create Support Ticket" button
    page.click('a[href="/admin/reportesSoporte"]')

    # Fill out the form
    page.fill('input[name="asunto"]', "Test Ticket")
    page.fill('textarea[name="descripcion"]', "This is a test ticket description.")
    page.select_option('select[name="rol_problema"]', "admin")
    page.select_option('select[name="prioridad"]', "Alta")

    # Take a screenshot
    page.screenshot(path="jules-scratch/verification/verification.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)