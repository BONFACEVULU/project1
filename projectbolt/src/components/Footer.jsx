import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import { FaFacebook, FaInstagram, FaTwitter, FaYoutube } from 'react-icons/fa';

function Footer() {
  return (
    <footer className="footer bg-dark text-white py-5">
      <Container>
        <Row>
          <Col md={4}>
            <h5>Dance Studio</h5>
            <p>Inspiring dancers since 1970</p>
            <div className="social-icons">
              <FaFacebook className="me-3" />
              <FaInstagram className="me-3" />
              <FaTwitter className="me-3" />
              <FaYoutube />
            </div>
          </Col>
          <Col md={4}>
            <h5>Quick Links</h5>
            <ul className="list-unstyled">
              <li><a href="/classes">Classes</a></li>
              <li><a href="/schedule">Schedule</a></li>
              <li><a href="/pricing">Pricing</a></li>
              <li><a href="/contact">Contact</a></li>
            </ul>
          </Col>
          <Col md={4}>
            <h5>Contact Us</h5>
            <ul className="list-unstyled">
              <li>123 Dance Street</li>
              <li>City, State 12345</li>
              <li>Phone: (123) 456-7890</li>
              <li>Email: info@dancestudio.com</li>
            </ul>
          </Col>
        </Row>
        <Row className="mt-4">
          <Col className="text-center">
            <p className="mb-0">© 2024 Dance Studio. All rights reserved.</p>
          </Col>
        </Row>
      </Container>
    </footer>
  );
}

export default Footer;