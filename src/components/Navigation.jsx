import React from 'react';
import { Navbar, Nav, Container } from 'react-bootstrap';
import { Link } from 'react-router-dom';

function Navigation() {
  return (
    <Navbar expand="lg" className="navbar-custom" fixed="top">
      <Container>
        <Navbar.Brand as={Link} to="/">Dance Studio</Navbar.Brand>
        <Navbar.Toggle aria-controls="basic-navbar-nav" />
        <Navbar.Collapse id="basic-navbar-nav">
          <Nav className="ms-auto">
            <Nav.Link as={Link} to="/">Home</Nav.Link>
            <Nav.Link as={Link} to="/classes">Classes</Nav.Link>
            <Nav.Link as={Link} to="/schedule">Schedule</Nav.Link>
            <Nav.Link as={Link} to="/instructors">Instructors</Nav.Link>
            <Nav.Link as={Link} to="/pricing">Pricing</Nav.Link>
            <Nav.Link as={Link} to="/contact">Contact</Nav.Link>
            <Nav.Link as={Link} to="/login" className="btn btn-outline-light ms-2">Login</Nav.Link>
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
}

export default Navigation/ /   T h i s   c o m p o n e n t   r e n d e r s   t h e   n a v i g a t i o n   b a r   f o r   t h e   a p p l i c a t i o n .  
 