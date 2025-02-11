import React from 'react';
import { Container, Row, Col, Button, Card } from 'react-bootstrap';
import { FaUsers, FaDancefloor, FaChild, FaCalendar } from 'react-icons/fa';

function Home() {
  return (
    <>
      {/* Hero Section */}
      <section className="hero-section">
        <div className="overlay"></div>
        <Container>
          <Row className="hero-content">
            <Col md={8} className="text-white">
              <h1 data-aos="fade-up">Welcome to Our Dance Studio</h1>
              <p data-aos="fade-up" data-aos-delay="200">
                Discover the joy of dance with over 400 weekly classes in 40+ styles
              </p>
              <Button variant="primary" size="lg" data-aos="fade-up" data-aos-delay="400">
                Start Dancing Today
              </Button>
            </Col>
          </Row>
        </Container>
      </section>

      {/* Features Section */}
      <section className="features-section py-5">
        <Container>
          <h2 className="text-center mb-5">Why Choose Us</h2>
          <Row>
            <Col md={3} className="text-center mb-4" data-aos="fade-up">
              <div className="feature-box">
                <FaUsers className="feature-icon" />
                <h4>All Levels Welcome</h4>
                <p>From beginners to professionals</p>
              </div>
            </Col>
            <Col md={3} className="text-center mb-4" data-aos="fade-up" data-aos-delay="200">
              <div className="feature-box">
                <FaDancefloor className="feature-icon" />
                <h4>40+ Dance Styles</h4>
                <p>Diverse range of classes</p>
              </div>
            </Col>
            <Col md={3} className="text-center mb-4" data-aos="fade-up" data-aos-delay="400">
              <div className="feature-box">
                <FaChild className="feature-icon" />
                <h4>Kids Classes</h4>
                <p>Special programs for children</p>
              </div>
            </Col>
            <Col md={3} className="text-center mb-4" data-aos="fade-up" data-aos-delay="600">
              <div className="feature-box">
                <FaCalendar className="feature-icon" />
                <h4>Flexible Schedule</h4>
                <p>Classes 7 days a week</p>
              </div>
            </Col>
          </Row>
        </Container>
      </section>

      {/* Classes Preview */}
      <section className="classes-section py-5 bg-light">
        <Container>
          <h2 className="text-center mb-5">Popular Classes</h2>
          <Row>
            {['Ballet', 'Hip Hop', 'Contemporary', 'Jazz'].map((style, index) => (
              <Col md={3} key={style} data-aos="fade-up" data-aos-delay={index * 200}>
                <Card className="class-card mb-4">
                  <Card.Img variant="top" src={`/images/${style.toLowerCase()}.jpg`} />
                  <Card.Body>
                    <Card.Title>{style}</Card.Title>
                    <Card.Text>
                      Experience the art of {style} with our expert instructors.
                    </Card.Text>
                    <Button variant="outline-primary">Learn More</Button>
                  </Card.Body>
                </Card>
              </Col>
            ))}
          </Row>
        </Container>
      </section>

      {/* Call to Action */}
      <section className="cta-section text-white text-center py-5">
        <Container>
          <h2 data-aos="fade-up">Ready to Start Your Dance Journey?</h2>
          <p data-aos="fade-up" data-aos-delay="200">
            Join our community of dancers and express yourself through movement
          </p>
          <Button variant="light" size="lg" data-aos="fade-up" data-aos-delay="400">
            Book Your First Class
          </Button>
        </Container>
      </section>
    </>
  );
}

export default Home;