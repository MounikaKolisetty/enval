import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { MatIconModule } from '@angular/material/icon'; // Import MatIconModule


@Component({
  selector: 'app-home',
  standalone: true,
  imports: [NavbarComponent, MatIconModule],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {

}
