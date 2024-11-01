import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-training',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive
  ],
  templateUrl: './training.component.html',
  styleUrl: './training.component.css'
})
export class TrainingComponent {

}
