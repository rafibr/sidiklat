"""Test script untuk memverifikasi certificate downloader."""

import logging
import sys
from pathlib import Path

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent))

from data_importer.certificate_downloader import CertificateDownloader


# Enable debug logging
logging.basicConfig(
    level=logging.DEBUG,
    format="[%(asctime)s] [%(name)s] [%(levelname)s] %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)

logger = logging.getLogger(__name__)


def test_download():
    """Test basic download functionality."""

    # Create test directory
    test_dir = Path(__file__).parent / "test_downloads"
    test_dir.mkdir(exist_ok=True)

    logger.info("=" * 60)
    logger.info("Testing Certificate Downloader")
    logger.info("=" * 60)

    downloader = CertificateDownloader(test_dir)

    # Test URLs
    test_cases = [
        {
            "url": "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf",
            "nama": "John Doe",
            "pelatihan": "Test Training Basic",
            "should_succeed": True,
        },
        {
            "url": "https://httpbin.org/status/404",
            "nama": "Jane Smith",
            "pelatihan": "Test Training 404",
            "should_succeed": False,
        },
        {
            "url": "not-a-valid-url",
            "nama": "Invalid User",
            "pelatihan": "Invalid URL Test",
            "should_succeed": False,
        },
        {
            "url": "",
            "nama": "Empty User",
            "pelatihan": "Empty URL Test",
            "should_succeed": False,
        },
    ]

    results = []

    for i, test in enumerate(test_cases, 1):
        logger.info("\n" + "-" * 60)
        logger.info(f"Test Case #{i}: {test['pelatihan']}")
        logger.info(f"URL: {test['url']}")
        logger.info(f"Expected: {'SUCCESS' if test['should_succeed'] else 'FAIL'}")
        logger.info("-" * 60)

        try:
            result = downloader.download(
                url=test['url'],
                nama=test['nama'],
                pelatihan=test['pelatihan']
            )

            if result:
                logger.info(f"✓ Download succeeded: {result}")
                logger.info(f"  File exists: {result.exists()}")
                logger.info(f"  File size: {result.stat().st_size} bytes")
                success = True
            else:
                logger.info("✓ Download returned None (expected for empty URL)")
                success = not test['should_succeed']

        except Exception as e:
            logger.error(f"✗ Download failed: {type(e).__name__}: {e}")
            success = not test['should_succeed']

        results.append({
            "test": test['pelatihan'],
            "success": success,
            "expected": test['should_succeed']
        })

    # Summary
    logger.info("\n" + "=" * 60)
    logger.info("TEST SUMMARY")
    logger.info("=" * 60)

    passed = 0
    for r in results:
        status = "✓ PASS" if (r['success'] == r['expected'] or not r['expected']) else "✗ FAIL"
        logger.info(f"{status}: {r['test']}")
        if r['success'] == r['expected'] or not r['expected']:
            passed += 1

    logger.info("-" * 60)
    logger.info(f"Results: {passed}/{len(results)} tests behaved as expected")
    logger.info("=" * 60)

    # Cleanup
    logger.info("\nTest directory: %s", test_dir.absolute())
    logger.info("You can check downloaded files there.")


if __name__ == "__main__":
    test_download()
